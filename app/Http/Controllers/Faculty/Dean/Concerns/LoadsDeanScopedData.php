<?php

namespace App\Http\Controllers\Faculty\Dean\Concerns;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait LoadsDeanScopedData
{
    private function deanFacultyIds(): Collection
    {
        $user = auth()->user();

        if (!$user) {
            return collect();
        }

        $facultyIds = Faculty::where('dean_id', $user->id)->pluck('id');

        $profileFacultyId = $user->facultyProfile?->department?->faculty_id;
        if ($profileFacultyId) {
            $facultyIds->push($profileFacultyId);
        }

        return $facultyIds->filter()->unique()->values();
    }

    private function deanDepartmentsQuery(): Builder
    {
        $facultyIds = $this->deanFacultyIds();

        return Department::query()
            ->with('faculty')
            ->when(
                $facultyIds->isNotEmpty(),
                fn ($query) => $query->whereIn('faculty_id', $facultyIds),
                fn ($query) => $query->whereRaw('1 = 0')
            );
    }

    private function deanFacultyMembersQuery(): Builder
    {
        $facultyIds = $this->deanFacultyIds();

        return User::query()
            ->with('facultyProfile.department.faculty')
            ->where(function ($query) {
                $query->where('role', 'faculty')
                    ->orWhereHas('roleCatalog', function ($roleQuery) {
                        $roleQuery->where('guard_role', 'faculty')
                            ->orWhereIn('slug', ['faculty', 'lecturer', 'head_of_department', 'dean']);
                    });
            })
            ->when($facultyIds->isNotEmpty(), function ($query) use ($facultyIds) {
                $query->where(function ($scopedQuery) use ($facultyIds) {
                    $scopedQuery->whereHas('facultyProfile.department', function ($departmentQuery) use ($facultyIds) {
                        $departmentQuery->whereIn('faculty_id', $facultyIds);
                    })->orWhereHas('taughtCourses.department', function ($departmentQuery) use ($facultyIds) {
                        $departmentQuery->whereIn('faculty_id', $facultyIds);
                    });
                });
            }, fn ($query) => $query->whereRaw('1 = 0'));
    }

    private function deanStudentsQuery(): Builder
    {
        $facultyIds = $this->deanFacultyIds();

        return User::query()
            ->with('studentProfile.department.faculty', 'studentProfile.program.department.faculty')
            ->where(function ($query) {
                $query->where('role', 'student')
                    ->orWhereHas('roleCatalog', function ($roleQuery) {
                        $roleQuery->where('guard_role', 'student')
                            ->orWhere('slug', 'student');
                    });
            })
            ->when($facultyIds->isNotEmpty(), function ($query) use ($facultyIds) {
                $query->where(function ($scopedQuery) use ($facultyIds) {
                    $scopedQuery->whereHas('studentProfile.department', function ($departmentQuery) use ($facultyIds) {
                        $departmentQuery->whereIn('faculty_id', $facultyIds);
                    })->orWhereHas('studentProfile.program.department', function ($departmentQuery) use ($facultyIds) {
                        $departmentQuery->whereIn('faculty_id', $facultyIds);
                    });
                });
            }, fn ($query) => $query->whereRaw('1 = 0'));
    }

    private function deanProgramsQuery(): Builder
    {
        $facultyIds = $this->deanFacultyIds();

        return Program::query()
            ->with('department.faculty')
            ->when(
                $facultyIds->isNotEmpty(),
                fn ($query) => $query->whereHas('department', fn ($departmentQuery) => $departmentQuery->whereIn('faculty_id', $facultyIds)),
                fn ($query) => $query->whereRaw('1 = 0')
            );
    }

    private function systemCurrencyCode(): string
    {
        return SystemSetting::getSetting('default_currency', config('currencies.default', 'USD'));
    }
}
