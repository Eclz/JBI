@extends('layouts.app')

@section('title', 'Course Materials - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Course Materials</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->name }}</a></li>
                            <li class="breadcrumb-item active">Materials</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        <i class="bi bi-plus-circle"></i> Add Material
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Course Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1">{{ $course->name }}</h5>
                            <p class="text-muted mb-0">{{ $course->code }} • {{ $course->credits }} Credits</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary fs-6">{{ $materials->total() }} Materials</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Grid -->
            @if($materials->count() > 0)
                <div class="row">
                    @foreach($materials as $material)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="material-icon">
                                            <i class="bi {{ $material->icon }} text-{{ $material->color }} fs-1"></i>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($material->type !== 'link')
                                                    <li><a class="dropdown-item" href="{{ $material->file_url }}" target="_blank">
                                                        <i class="bi bi-eye me-2"></i>View
                                                    </a></li>
                                                    @if($material->is_downloadable)
                                                        <li><a class="dropdown-item" href="{{ $material->file_url }}" download="{{ $material->file_name }}">
                                                            <i class="bi bi-download me-2"></i>Download
                                                        </a></li>
                                                    @endif
                                                @else
                                                    <li><a class="dropdown-item" href="{{ $material->external_url }}" target="_blank">
                                                        <i class="bi bi-box-arrow-up-right me-2"></i>Open Link
                                                    </a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $material->id }})">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h6 class="card-title">{{ $material->title }}</h6>
                                    @if($material->description)
                                        <p class="card-text text-muted small">{{ Str::limit($material->description, 100) }}</p>
                                    @endif

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $material->created_at->format('M d, Y') }}
                                            </small>
                                            @if($material->file_size_human)
                                                <small class="text-muted">
                                                    {{ $material->file_size_human }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark">{{ ucfirst($material->type) }}</span>
                                            @if($material->is_downloadable)
                                                <span class="badge bg-success">Downloadable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $materials->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-folder2-open display-1 text-muted"></i>
                        <h5 class="mt-3">No Materials Yet</h5>
                        <p class="text-muted">Start by adding your first course material.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                            <i class="bi bi-plus-circle me-2"></i>Add First Material
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.courses.materials.store', $course) }}" method="POST" enctype="multipart/form-data" id="materialForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required onchange="toggleFileInput()">
                                    <option value="">Select type...</option>
                                    <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Document</option>
                                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                                    <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image</option>
                                    <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>External Link</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/1000 characters
                        </div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="fileUploadSection">
                        <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept="*/*">
                        <div class="form-text">
                            Maximum file size: 50MB<br>
                            Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, MP4, MP3, JPG, PNG, etc.
                        </div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="linkSection" style="display: none;">
                        <label for="link_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                               id="link_url" name="link_url" value="{{ old('link_url') }}"
                               placeholder="https://example.com">
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order" class="form-label">Order</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror"
                                       id="order" name="order" value="{{ old('order', 0) }}" min="0">
                                <div class="form-text">Lower numbers appear first</div>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_downloadable"
                                           name="is_downloadable" value="1"
                                           {{ old('is_downloadable', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_downloadable">
                                        Allow downloads
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        <i class="bi bi-plus-circle me-2"></i>Add Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this material?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. The file will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Material
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize character counter
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');

    function updateCharCount() {
        const count = descriptionTextarea.value.length;
        charCount.textContent = count;

        if (count > 1000) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    }

    descriptionTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count

    // Form submission handling
    const form = document.getElementById('materialForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function() {
        const spinner = submitBtn.querySelector('.spinner-border');
        const icon = submitBtn.querySelector('.bi-plus-circle');

        spinner.classList.remove('d-none');
        icon.classList.add('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading...';
    });

    // Reset form when modal is closed
    const modal = document.getElementById('addMaterialModal');
    modal.addEventListener('hidden.bs.modal', function() {
        form.reset();
        updateCharCount();
        toggleFileInput();

        // Reset submit button
        const spinner = submitBtn.querySelector('.spinner-border');
        const icon = submitBtn.querySelector('.bi-plus-circle');

        if (spinner) spinner.classList.add('d-none');
        if (icon) icon.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Material';

        // Clear validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    });

    // Initialize toggle on page load
    toggleFileInput();
});

function toggleFileInput() {
    const type = document.getElementById('type').value;
    const fileSection = document.getElementById('fileUploadSection');
    const linkSection = document.getElementById('linkSection');
    const fileInput = document.getElementById('file');
    const linkInput = document.getElementById('link_url');

    if (type === 'link') {
        fileSection.style.display = 'none';
        linkSection.style.display = 'block';
        fileInput.required = false;
        linkInput.required = true;
        // Clear file input when switching to link
        fileInput.value = '';
    } else if (type !== '') {
        fileSection.style.display = 'block';
        linkSection.style.display = 'none';
        fileInput.required = true;
        linkInput.required = false;
        // Clear link input when switching to file
        linkInput.value = '';
    } else {
        // No type selected
        fileSection.style.display = 'block';
        linkSection.style.display = 'none';
        fileInput.required = false;
        linkInput.required = false;
    }
}

function confirmDelete(materialId) {
    const form = document.getElementById('deleteForm');
    form.action = `{{ route('admin.courses.materials.store', $course) }}`.replace('/materials', `/materials/${materialId}`);

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush

@push('styles')
<style>
.material-icon {
    text-align: center;
    margin-bottom: 1rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.dropdown-toggle::after {
    display: none;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>
@endpush
