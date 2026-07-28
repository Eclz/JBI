@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">Gradebook - {{ $course->name }}</h2>
            <p class="text-muted mb-0">{{ $course->code }} | {{ $course->semester->name ?? 'N/A' }}</p>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <button class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Export
            </button>
            <a href="{{ route('faculty.grading.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Gradebook Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <tr>
                            <th class="px-4 py-3" style="position: sticky; left: 0; background-color: #f8f9fa; z-index: 10;">Student</th>
                            <th class="px-3 py-3 text-center" style="position: sticky; left: 200px; background-color: #f8f9fa; z-index: 10;">ID</th>
                            @foreach($assignments as $assignment)
                                <th class="px-3 py-3 text-center" style="min-width: 120px;">
                                    <div style="font-size: 0.875rem; font-weight: 600;">{{ $assignment->title }}</div>
                                    <div style="font-size: 0.75rem; color: #6c757d;">{{ $assignment->points }}pts</div>
                                </th>
                            @endforeach
                            <th class="px-3 py-3 text-center" style="background-color: #e3f2fd; font-weight: 600;">Total</th>
                            <th class="px-3 py-3 text-center" style="background-color: #e8f5e9; font-weight: 600;">Percentage</th>
                            <th class="px-3 py-3 text-center" style="background-color: #fff3e0; font-weight: 600;">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $totalEarned = 0;
                                $totalPossible = 0;
                                $studentGrades = $student->grades->keyBy('assignment_id');
                            @endphp
                            <tr id="student-{{ $student->id }}">
                                <td class="px-4 py-3" style="position: sticky; left: 0; background-color: white; z-index: 5;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 600;">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 500; color: #1a202c;">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div style="font-size: 0.875rem; color: #6c757d;">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center" style="position: sticky; left: 200px; background-color: white; z-index: 5;">
                                    <span class="badge bg-secondary">{{ $student->studentProfile->admission_number ?? 'N/A' }}</span>
                                </td>
                                @foreach($assignments as $assignment)
                                    @php
                                        $grade = $studentGrades->get($assignment->id);
                                        if ($grade && $grade->is_published) {
                                            $totalEarned += $grade->points_earned;
                                            $totalPossible += $grade->points_possible;
                                        }
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        @if($grade && $grade->is_published)
                                            <div style="font-weight: 600; color: {{ $grade->percentage >= 70 ? '#059669' : '#dc2626' }};">
                                                {{ number_format($grade->points_earned, 1) }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #6c757d;">{{ number_format($grade->percentage, 1) }}%</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-3 text-center" style="background-color: #f8f9fa; font-weight: 600;">
                                    {{ number_format($totalEarned, 1) }} / {{ number_format($totalPossible, 1) }}
                                </td>
                                <td class="px-3 py-3 text-center" style="background-color: #f8f9fa; font-weight: 600;">
                                    @php
                                        $percentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;
                                    @endphp
                                    <span style="color: {{ $percentage >= 70 ? '#059669' : '#dc2626' }};">
                                        {{ number_format($percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center" style="background-color: #f8f9fa;">
                                    @php
                                        $letterGrade = 'F';
                                        $badgeClass = 'bg-danger';
                                        if ($percentage >= 90) {
                                            $letterGrade = 'A';
                                            $badgeClass = 'bg-success';
                                        } elseif ($percentage >= 80) {
                                            $letterGrade = 'B';
                                            $badgeClass = 'bg-info';
                                        } elseif ($percentage >= 70) {
                                            $letterGrade = 'C';
                                            $badgeClass = 'bg-primary';
                                        } elseif ($percentage >= 60) {
                                            $letterGrade = 'D';
                                            $badgeClass = 'bg-warning';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">{{ $letterGrade }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($assignments) + 6 }}" class="text-center py-5">
                                    <i class="bi bi-people" style="font-size: 3rem; color: #cbd5e0;"></i>
                                    <p class="mt-3 mb-0 text-muted">No students enrolled in this course</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grade Distribution Chart -->
    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #1a202c; font-weight: 600;">Grade Distribution</h5>
                </div>
                <div class="card-body">
                    @php
                        $gradeDistribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
                        foreach($students as $student) {
                            $totalEarned = 0;
                            $totalPossible = 0;
                            $studentGrades = $student->grades->keyBy('assignment_id');
                            foreach($assignments as $assignment) {
                                $grade = $studentGrades->get($assignment->id);
                                if ($grade && $grade->is_published) {
                                    $totalEarned += $grade->points_earned;
                                    $totalPossible += $grade->points_possible;
                                }
                            }
                            $percentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;
                            if ($percentage >= 90) $gradeDistribution['A']++;
                            elseif ($percentage >= 80) $gradeDistribution['B']++;
                            elseif ($percentage >= 70) $gradeDistribution['C']++;
                            elseif ($percentage >= 60) $gradeDistribution['D']++;
                            else $gradeDistribution['F']++;
                        }
                        $maxCount = max($gradeDistribution) ?: 1;
                    @endphp
                    <div class="row g-3">
                        @foreach($gradeDistribution as $grade => $count)
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge
                                        @if($grade == 'A') bg-success
                                        @elseif($grade == 'B') bg-info
                                        @elseif($grade == 'C') bg-primary
                                        @elseif($grade == 'D') bg-warning
                                        @else bg-danger
                                        @endif
                                        me-3" style="width: 40px; padding: 0.5rem;">{{ $grade }}</span>
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar
                                                @if($grade == 'A') bg-success
                                                @elseif($grade == 'B') bg-info
                                                @elseif($grade == 'C') bg-primary
                                                @elseif($grade == 'D') bg-warning
                                                @else bg-danger
                                                @endif"
                                                role="progressbar"
                                                style="width: {{ ($count / $maxCount) * 100 }}%"
                                                aria-valuenow="{{ $count }}"
                                                aria-valuemin="0"
                                                aria-valuemax="{{ $maxCount }}">
                                                <span style="font-weight: 600;">{{ $count }} student{{ $count != 1 ? 's' : '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #1a202c; font-weight: 600;">Class Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        $allPercentages = [];
                        foreach($students as $student) {
                            $totalEarned = 0;
                            $totalPossible = 0;
                            $studentGrades = $student->grades->keyBy('assignment_id');
                            foreach($assignments as $assignment) {
                                $grade = $studentGrades->get($assignment->id);
                                if ($grade && $grade->is_published) {
                                    $totalEarned += $grade->points_earned;
                                    $totalPossible += $grade->points_possible;
                                }
                            }
                            if ($totalPossible > 0) {
                                $allPercentages[] = ($totalEarned / $totalPossible) * 100;
                            }
                        }
                        $classAverage = count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0;
                        $highestGrade = count($allPercentages) > 0 ? max($allPercentages) : 0;
                        $lowestGrade = count($allPercentages) > 0 ? min($allPercentages) : 0;
                    @endphp
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background-color: #e3f2fd;">
                                <div style="font-size: 2rem; font-weight: 700; color: #1976d2;">{{ number_format($classAverage, 1) }}%</div>
                                <div style="color: #1976d2; font-weight: 500;">Class Average</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background-color: #e8f5e9;">
                                <div style="font-size: 2rem; font-weight: 700; color: #388e3c;">{{ number_format($highestGrade, 1) }}%</div>
                                <div style="color: #388e3c; font-weight: 500;">Highest Grade</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background-color: #fff3e0;">
                                <div style="font-size: 2rem; font-weight: 700; color: #f57c00;">{{ number_format($lowestGrade, 1) }}%</div>
                                <div style="color: #f57c00; font-weight: 500;">Lowest Grade</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background-color: #fce4ec;">
                                <div style="font-size: 2rem; font-weight: 700; color: #c2185b;">{{ $students->count() }}</div>
                                <div style="color: #c2185b; font-weight: 500;">Total Students</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn-group, .alert {
            display: none;
        }
    }
</style>
@endsection
