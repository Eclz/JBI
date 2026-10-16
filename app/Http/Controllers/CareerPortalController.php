<?php

namespace App\Http\Controllers;

use App\Models\HrApplicant;
use App\Models\HrVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CareerPortalController extends Controller
{
    public function index()
    {
        $vacancies = HrVacancy::query()
            ->where('status', 'Open')
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('closing_date')->orWhereDate('closing_date', '>=', today());
            })
            ->latest('opening_date')
            ->get();

        return view('careers.index', compact('vacancies'));
    }

    public function show(HrVacancy $vacancy)
    {
        abort_unless($this->isPubliclyOpen($vacancy), 404);

        return view('careers.show', compact('vacancy'));
    }

    public function apply(Request $request, HrVacancy $vacancy)
    {
        abort_unless($this->isPubliclyOpen($vacancy), 404);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'cover_letter' => ['nullable', 'string', 'max:10000'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'documents' => ['nullable', 'array', 'max:8'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'consent' => ['accepted'],
        ]);

        $email = Str::lower(trim($data['email']));
        $alreadyApplied = HrApplicant::query()
            ->where('hr_vacancy_id', $vacancy->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($alreadyApplied) {
            throw ValidationException::withMessages([
                'email' => 'An application has already been submitted for this vacancy using this email address.',
            ]);
        }

        $directory = 'hr-applications/' . $vacancy->id . '/' . Str::uuid();
        $cvPath = $request->file('cv')->store($directory, 'local');
        if (!$cvPath) {
            throw new \RuntimeException('Could not securely store the uploaded CV.');
        }
        $documentPaths = [];

        try {
            foreach ($request->file('documents', []) as $document) {
                $documentPath = $document->store($directory, 'local');
                if (!$documentPath) {
                    throw new \RuntimeException('Could not securely store an uploaded supporting document.');
                }
                $documentPaths[] = $documentPath;
            }

            $trackingToken = Str::random(64);
            $applicant = DB::transaction(fn () => HrApplicant::create([
                'hr_vacancy_id' => $vacancy->id,
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'email' => $email,
                'phone' => trim($data['phone']),
                'cv_path' => $cvPath,
                'documents' => $documentPaths,
                'tracking_token_hash' => hash('sha256', $trackingToken),
                'cover_letter' => $data['cover_letter'] ?? null,
                'consent_at' => now(),
                'status' => 'Applied',
            ]));
        } catch (Throwable $exception) {
            Storage::disk('local')->delete(array_merge([$cvPath], $documentPaths));
            throw $exception;
        }

        return view('careers.submitted', [
            'applicant' => $applicant,
            'trackingUrl' => route('careers.track', ['token' => $trackingToken]),
        ]);
    }

    public function track(string $token)
    {
        abort_unless(preg_match('/\A[A-Za-z0-9]{64}\z/', $token) === 1, 404);

        $applicant = HrApplicant::query()
            ->with('vacancy:id,position_title')
            ->where('tracking_token_hash', hash('sha256', $token))
            ->firstOrFail();

        return view('careers.track', compact('applicant'));
    }

    private function isPubliclyOpen(HrVacancy $vacancy): bool
    {
        return $vacancy->status === 'Open'
            && (!$vacancy->published_at || $vacancy->published_at->isPast() || $vacancy->published_at->isNow())
            && (!$vacancy->closing_date || $vacancy->closing_date->gte(today()));
    }
}
