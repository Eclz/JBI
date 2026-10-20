<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;

class ApplicationFileController extends Controller
{
    public function show(Application $application, string $file)
    {
        $user = auth()->user();
        abort_unless(strtolower($user->email) === strtolower($application->email)
            || $user->hasPermission('applications', 'view'), 403);
        $path = $file === 'receipt' ? $application->payment_proof : ($application->documents[$file] ?? null);
        $path = is_array($path) ? ($path['path'] ?? null) : $path;
        abort_unless(is_string($path) && $path && ! str_contains($path, '..'), 404);
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            return Storage::disk($disk)->response($path, null, ['X-Content-Type-Options' => 'nosniff']);
        }

        return Storage::disk($disk)->download($path);
    }
}
