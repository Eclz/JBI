<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam in Progress - {{ $exam->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Timer Header (Fixed) -->
    <div class="fixed top-0 left-0 right-0 bg-red-600 text-white p-4 shadow-lg z-50">
        <div class="container mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold">{{ $exam->title }}</h1>
                <p class="text-sm opacity-90">{{ $exam->course->name }}</p>
            </div>

            <div class="text-center">
                <p class="text-sm mb-1">Time Remaining</p>
                <div id="timer" class="text-3xl font-bold" data-end-time="{{ $endTime }}">
                    <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                </div>
            </div>

            <div class="text-right">
                <button type="button" onclick="submitExam()"
                        class="bg-white text-red-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Exam
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 mt-24">
        <form id="examForm" action="{{ route('student.exams.submit', $attempt) }}" method="POST">
            @csrf

            <!-- Instructions -->
            @if($exam->instructions)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
                <h3 class="text-lg font-bold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Instructions
                </h3>
                <div class="text-blue-900">
                    {!! nl2br(e($exam->instructions)) !!}
                </div>
            </div>
            @endif

            <!-- Answer Editor -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-pen text-blue-600 mr-2"></i>
                    Your Answers
                </h2>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Write your answers below:
                    </label>
                    <textarea name="answers" id="answers" rows="30"
                              class="w-full border-2 border-gray-300 rounded-lg p-4 focus:border-blue-500 focus:outline-none font-mono"
                              placeholder="Type your answers here...

Example format:
1. Answer to question 1...
2. Answer to question 2...
3. Answer to question 3..."
                              required>{{ old('answers', $attempt->answers) }}</textarea>
                </div>

                <!-- Character/Word Counter -->
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        <span id="wordCount">0</span> words |
                        <span id="charCount">0</span> characters
                    </div>
                    <div class="text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Auto-saved every 30 seconds
                    </div>
                </div>
            </div>

            <!-- Submit Button (Bottom) -->
            <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <p class="text-gray-700">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Make sure to review your answers before submitting
                    </p>
                    <button type="button" onclick="submitExam()"
                            class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>
                        Submit Exam
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Timer countdown
        const endTime = new Date(document.getElementById('timer').dataset.endTime).getTime();

        const timerInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(timerInterval);
                document.getElementById('timer').innerHTML = 'TIME UP!';
                submitExam();
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');

            // Warning when less than 5 minutes
            if (distance < 5 * 60 * 1000) {
                document.getElementById('timer').classList.add('animate-pulse');
            }
        }, 1000);

        // Word and character counter
        const textarea = document.getElementById('answers');
        textarea.addEventListener('input', () => {
            const text = textarea.value;
            const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
            const chars = text.length;

            document.getElementById('wordCount').textContent = words;
            document.getElementById('charCount').textContent = chars;
        });

        // Auto-save every 30 seconds
        setInterval(() => {
            const formData = new FormData(document.getElementById('examForm'));
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('auto_save', '1');

            fetch('{{ route('student.exams.auto-save', $attempt) }}', {
                method: 'POST',
                body: formData
            });
        }, 30000);

        // Submit exam function
        function submitExam() {
            if (confirm('Are you sure you want to submit your exam? This action cannot be undone.')) {
                document.getElementById('examForm').submit();
            }
        }

        // Prevent accidental page close
        window.addEventListener('beforeunload', (e) => {
            e.preventDefault();
            e.returnValue = '';
        });

        // Prevent right-click
        document.addEventListener('contextmenu', (e) => e.preventDefault());
    </script>
</body>
</html>
