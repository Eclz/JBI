@extends('layouts.app')

@section('title', 'Academic Registrar Grading Criteria')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-award me-2"></i>Academic Registrar Grading Criteria</h2>
                    <p class="text-muted mb-0">Official university grading scale and criteria used for automated course grading, GPA calculations, and student transcripts.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.registrar.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Registrar Dashboard
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-journal-text me-1"></i>Course Enrollments
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-sliders me-2"></i>Official Grading Scale & Criteria</h5>
            <span class="badge bg-light text-primary">Academic Registrar Policy</span>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                    <div>
                        <strong>Automated Grading Policy:</strong> Student grade points, final percentages, and letter grades are automatically calculated from continuous assessment results, assignments, and exam submissions using the criteria defined below.
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.registrar.grading-criteria.update') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle border mb-4" id="criteriaTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Min Percentage (%)</th>
                                <th style="width: 15%;">Max Percentage (%)</th>
                                <th style="width: 15%;">Letter Grade</th>
                                <th style="width: 15%;">Grade Points (GP)</th>
                                <th>Classification / Remark</th>
                                <th style="width: 8%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gradingCriteria as $idx => $rule)
                                <tr>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" name="criteria[{{ $idx }}][min]" class="form-control" value="{{ old('criteria.' . $idx . '.min', $rule['min']) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" name="criteria[{{ $idx }}][max]" class="form-control" value="{{ old('criteria.' . $idx . '.max', $rule['max']) }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="criteria[{{ $idx }}][letter]" class="form-control text-uppercase fw-bold" value="{{ old('criteria.' . $idx . '.letter', $rule['letter']) }}" maxlength="5" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="5" name="criteria[{{ $idx }}][grade_points]" class="form-control" value="{{ old('criteria.' . $idx . '.grade_points', $rule['grade_points']) }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="criteria[{{ $idx }}][remark]" class="form-control" value="{{ old('criteria.' . $idx . '.remark', $rule['remark'] ?? '') }}" placeholder="e.g. Excellent, Pass">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-success" id="addRowBtn">
                        <i class="bi bi-plus-circle me-1"></i>Add Grade Tier
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i>Save Grading Criteria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let rowIdx = {{ count($gradingCriteria) }};

        $('#addRowBtn').on('click', function() {
            const rowHtml = `
                <tr>
                    <td><input type="number" step="0.01" min="0" max="100" name="criteria[${rowIdx}][min]" class="form-control" required placeholder="0"></td>
                    <td><input type="number" step="0.01" min="0" max="100" name="criteria[${rowIdx}][max]" class="form-control" required placeholder="100"></td>
                    <td><input type="text" name="criteria[${rowIdx}][letter]" class="form-control text-uppercase fw-bold" required maxlength="5" placeholder="A"></td>
                    <td><input type="number" step="0.01" min="0" max="5" name="criteria[${rowIdx}][grade_points]" class="form-control" required placeholder="4.0"></td>
                    <td><input type="text" name="criteria[${rowIdx}][remark]" class="form-control" placeholder="Classification"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#criteriaTable tbody').append(rowHtml);
            rowIdx++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#criteriaTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('At least one grading tier must remain defined.');
            }
        });
    });
</script>
@endpush
