<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentsImport implements OnEachRow, WithHeadingRow
{
    private int $created = 0;
    private int $skipped = 0;
    private int $errorCount = 0;
    private array $rowErrors = [];
    private array $skippedRows = [];
    private array $seenEmails = [];
    private array $seenAdmissionNumbers = [];
    private Role $studentRole;

    public function __construct()
    {
        $this->studentRole = Role::where('slug', 'student')
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function onRow(Row $row): void
    {
        $rowNumber = $row->getIndex();
        $data = $row->toArray();

        if (!array_filter($data, fn ($value) => $value !== null && trim((string) $value) !== '')) {
            return;
        }

        $data = [
            'first_name' => trim((string) ($data['first_name'] ?? '')),
            'last_name' => trim((string) ($data['last_name'] ?? '')),
            'email' => Str::lower(trim((string) ($data['email'] ?? ''))),
            'admission_number' => trim((string) ($data['admission_number'] ?? '')),
            'admission_date' => $this->normalizeDate($data['admission_date'] ?? null),
            'department' => trim((string) ($data['department'] ?? '')),
            'program' => trim((string) ($data['program'] ?? '')),
            'status' => Str::lower(trim((string) ($data['status'] ?? 'active'))) ?: 'active',
            'current_semester' => $this->nullableString($data['current_semester'] ?? null),
            'year_of_study' => $this->nullableString($data['year_of_study'] ?? null),
            'phone' => $this->nullableString($data['phone'] ?? null),
            'date_of_birth' => $this->normalizeDate($data['date_of_birth'] ?? null),
            'gender' => Str::lower(trim((string) ($data['gender'] ?? ''))) ?: null,
            'address' => $this->nullableString($data['address'] ?? null),
        ];

        $validator = Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'admission_number' => ['required', 'string', 'max:255'],
            'admission_date' => ['required', 'date_format:Y-m-d'],
            'department' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,graduated,dropped,suspended'],
            'current_semester' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:12'],
            'phone' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            $this->recordError($rowNumber, $validator->errors()->all());
            return;
        }

        $email = $data['email'];
        $admissionNumber = $data['admission_number'];
        $emailKey = Str::lower($email);
        $admissionKey = Str::lower($admissionNumber);

        if (isset($this->seenEmails[$emailKey]) || User::whereRaw('LOWER(email) = ?', [$emailKey])->exists()) {
            $this->recordSkipped($rowNumber, 'Email address already exists.');
            return;
        }

        if (
            isset($this->seenAdmissionNumbers[$admissionKey])
            || StudentProfile::whereRaw('LOWER(admission_number) = ?', [$admissionKey])->exists()
        ) {
            $this->recordSkipped($rowNumber, 'Admission number already exists.');
            return;
        }

        $department = Department::where('is_active', true)
            ->where(function ($query) use ($data) {
                $query->whereRaw('LOWER(name) = ?', [Str::lower($data['department'])])
                    ->orWhereRaw('LOWER(code) = ?', [Str::lower($data['department'])]);
            })
            ->first();

        if (!$department) {
            $this->recordError($rowNumber, ['Department does not match an active department name or code.']);
            return;
        }

        $program = Program::where('department_id', $department->id)
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [Str::lower($data['program'])])
            ->first();

        DB::transaction(function () use ($data, $department, $program): void {
            $name = trim($data['first_name'] . ' ' . $data['last_name']);
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'name' => $name,
                'email' => $data['email'],
                'password' => Hash::make(Str::random(64)),
                'role' => 'student',
                'role_id' => $this->studentRole->id,
                'student_id' => null,
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'] ?: null,
                'address' => $data['address'],
                'is_active' => true,
                'must_change_password' => true,
            ]);
            $user->syncRoles([$this->studentRole->id], $this->studentRole->id);

            StudentProfile::create([
                'user_id' => $user->id,
                'admission_number' => $data['admission_number'],
                'admission_date' => $data['admission_date'],
                'department_id' => $department->id,
                'program_id' => $program?->id,
                'program' => $data['program'],
                'current_semester' => $data['current_semester'] ?: 1,
                'year_of_study' => $data['year_of_study'],
                'status' => $data['status'],
                'application_status' => 'approved',
            ]);
        });

        $this->seenEmails[$emailKey] = true;
        $this->seenAdmissionNumbers[$admissionKey] = true;
        $this->created++;
    }

    public function getResults(): array
    {
        return [
            'created' => $this->created,
            'skipped' => $this->skipped,
            'errors' => $this->errorCount,
            'row_errors' => $this->rowErrors,
            'skipped_rows' => $this->skippedRows,
        ];
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            $serialDate = (float) $value;
            if ($serialDate < 1 || $serialDate > 2958465) {
                return (string) $value;
            }

            return ExcelDate::excelToDateTimeObject($serialDate)->format('Y-m-d');
        }

        $date = trim((string) $value);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts) && checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) {
            return $date;
        }

        return $date;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function recordError(int $rowNumber, array $messages): void
    {
        $this->errorCount++;
        if (count($this->rowErrors) < 20) {
            $this->rowErrors[] = ['row' => $rowNumber, 'messages' => $messages];
        }
    }

    private function recordSkipped(int $rowNumber, string $reason): void
    {
        $this->skipped++;
        if (count($this->skippedRows) < 20) {
            $this->skippedRows[] = ['row' => $rowNumber, 'reason' => $reason];
        }
    }
}
