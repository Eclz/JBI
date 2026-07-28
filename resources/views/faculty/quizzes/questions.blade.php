@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Manage Quiz Questions</h1>
            <p class="text-muted mb-0">{{ $quiz->title }} - {{ $quiz->course->code }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('faculty.quizzes.show', $quiz) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Quiz
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="bi bi-plus-circle me-2"></i>Add Question
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Questions ({{ $quiz->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($quiz->questions as $index => $question)
                        <div class="question-item p-3 mb-3 border rounded" style="background-color: #f8fafc;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0" style="color: #1e293b; font-weight: 600;">Question {{ $index + 1 }}</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editQuestionModal"
                                            data-action="{{ route('faculty.quizzes.questions.update', [$quiz, $question]) }}"
                                            data-question-text="{{ e($question->question) }}"
                                            data-question-type="{{ $question->question_type }}"
                                            data-points="{{ $question->points }}"
                                            data-options='@json($question->options ?? [])'
                                            data-correct-answer="{{ e($question->correct_answer) }}"
                                            data-explanation="{{ e($question->explanation) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('faculty.quizzes.questions.destroy', [$quiz, $question]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="mb-2" style="color: #475569;">{{ $question->question }}</p>

                            <div class="d-flex flex-wrap gap-3 mb-2">
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                <span class="badge bg-success">{{ $question->points }} points</span>
                            </div>

                            @if($question->question_type === 'multiple_choice' && $question->options)
                                <div class="mt-2">
                                    <strong style="color: #64748b; font-size: 0.875rem;">Options:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($question->options as $option)
                                            <li style="color: #64748b;">
                                                {{ $option }}
                                                @if($option === $question->correct_answer)
                                                    <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="mt-2">
                                    <strong style="color: #64748b; font-size: 0.875rem;">Correct Answer:</strong>
                                    <span class="text-success">{{ $question->correct_answer }}</span>
                                </div>
                            @endif

                            @if($question->explanation)
                                <div class="mt-2">
                                    <strong style="color: #64748b; font-size: 0.875rem;">Explanation:</strong>
                                    <p class="mb-0" style="color: #64748b;">{{ $question->explanation }}</p>
                                </div>
                            @endif
                        </div>

                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-question-circle display-1 text-muted"></i>
                            <p class="text-muted mt-3">No questions added yet. Click "Add Question" to get started.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0" style="color: #1e293b; font-weight: 600;">Quiz Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Total Questions</small>
                        <strong style="color: #1e293b;">{{ $quiz->questions->count() }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Total Points</small>
                        <strong style="color: #1e293b;">{{ $quiz->questions->sum('points') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Duration</small>
                        <strong style="color: #1e293b;">{{ $quiz->duration }} minutes</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Max Attempts</small>
                        <strong style="color: #1e293b;">{{ $quiz->max_attempts ?? 'Unlimited' }}</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Status</small>
                        @if($quiz->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning">Draft</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3" style="color: #1e293b; font-weight: 600;">Question Types</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span style="color: #64748b;">Multiple Choice</span>
                            <strong style="color: #1e293b;">{{ $quiz->questions->where('question_type', 'multiple_choice')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span style="color: #64748b;">True/False</span>
                            <strong style="color: #1e293b;">{{ $quiz->questions->where('question_type', 'true_false')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span style="color: #64748b;">Short Answer</span>
                            <strong style="color: #1e293b;">{{ $quiz->questions->where('question_type', 'short_answer')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Points</label>
                            <input type="number" name="points" class="form-control" required min="0" step="0.5">
                        </div>
                    </div>

                    <div class="mb-3" id="editOptionsField" style="display: none;">
                        <label class="form-label">Options (one per line)</label>
                        <textarea name="options" class="form-control" rows="4"></textarea>
                        <small class="text-muted">Enter each option on a new line</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correct Answer</label>
                        <input type="text" name="correct_answer" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Explanation (Optional)</label>
                        <textarea name="explanation" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('faculty.quizzes.questions.store', $quiz) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question Type</label>
                        <select name="question_type" id="questionType" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Points</label>
                            <input type="number" name="points" class="form-control" required min="0" step="0.5" value="1">
                        </div>
                    </div>

                    <div class="mb-3" id="optionsField" style="display: none;">
                        <label class="form-label">Options (one per line)</label>
                        <textarea name="options" class="form-control" rows="4"></textarea>
                        <small class="text-muted">Enter each option on a new line</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correct Answer</label>
                        <input type="text" name="correct_answer" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Explanation (Optional)</label>
                        <textarea name="explanation" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('questionType').addEventListener('change', function() {
    const optionsField = document.getElementById('optionsField');
    if (this.value === 'multiple_choice') {
        optionsField.style.display = 'block';
        optionsField.querySelector('textarea').required = true;
    } else {
        optionsField.style.display = 'none';
        optionsField.querySelector('textarea').required = false;
    }
});

const editQuestionModal = document.getElementById('editQuestionModal');
editQuestionModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    if (!button) {
        return;
    }

    const form = editQuestionModal.querySelector('form');
    form.action = button.getAttribute('data-action');

    const questionText = button.getAttribute('data-question-text') || '';
    const questionType = button.getAttribute('data-question-type') || '';
    const points = button.getAttribute('data-points') || '';
    const correctAnswer = button.getAttribute('data-correct-answer') || '';
    const explanation = button.getAttribute('data-explanation') || '';
    const optionsRaw = button.getAttribute('data-options') || '[]';

    editQuestionModal.querySelector('[name="question_text"]').value = questionText;
    editQuestionModal.querySelector('[name="points"]').value = points;
    editQuestionModal.querySelector('[name="correct_answer"]').value = correctAnswer;
    editQuestionModal.querySelector('[name="explanation"]').value = explanation;

    const optionsField = document.getElementById('editOptionsField');
    const optionsTextarea = optionsField.querySelector('textarea');
    if (questionType === 'multiple_choice') {
        let options = [];
        try {
            options = JSON.parse(optionsRaw);
        } catch (error) {
            options = [];
        }
        optionsTextarea.value = (options || []).join('\n');
        optionsTextarea.required = true;
        optionsField.style.display = 'block';
    } else {
        optionsTextarea.value = '';
        optionsTextarea.required = false;
        optionsField.style.display = 'none';
    }
});
</script>
@endsection
