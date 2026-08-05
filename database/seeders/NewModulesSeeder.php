<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Models\VotingSession;
use App\Models\VotingPosition;
use App\Models\VotingCandidate;
use App\Models\EvaluationSurvey;
use App\Models\EvaluationQuestion;

class NewModulesSeeder extends Seeder
{
    public function run(): void
    {
        $program = Program::first();
        $academicYear = AcademicYear::first();
        $semester = Semester::first();
        $facultyMember = User::where('role', 'faculty')->first() ?? User::where('role', 'admin')->first();
        $courses = Course::take(5)->get();

        // 1. Seed Timetables
        if ($courses->isNotEmpty()) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $timeSlots = [
                ['08:00', '10:00'],
                ['10:30', '12:30'],
                ['14:00', '16:00'],
            ];
            $venues = ['Lab 1 (Software Studio)', 'Lab 3 (AI Lab)', 'Main Auditorium A', 'Lecture Hall 2'];

            foreach ($courses as $index => $course) {
                $day = $days[$index % count($days)];
                $times = $timeSlots[$index % count($timeSlots)];
                $venue = $venues[$index % count($venues)];

                Timetable::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'day_of_week' => $day,
                        'type' => 'teaching',
                    ],
                    [
                        'faculty_id' => $facultyMember?->id,
                        'program_id' => $program?->id,
                        'academic_year_id' => $academicYear?->id,
                        'semester_id' => $semester?->id,
                        'year_of_study' => 1,
                        'semester_number' => 1,
                        'start_time' => $times[0],
                        'end_time' => $times[1],
                        'room_venue' => $venue,
                        'notes' => 'Regular weekly lecture session',
                    ]
                );

                // Exam slot
                Timetable::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'day_of_week' => $day,
                        'type' => 'exams',
                    ],
                    [
                        'faculty_id' => $facultyMember?->id,
                        'program_id' => $program?->id,
                        'academic_year_id' => $academicYear?->id,
                        'semester_id' => $semester?->id,
                        'year_of_study' => 1,
                        'semester_number' => 1,
                        'start_time' => '09:00',
                        'end_time' => '12:00',
                        'room_venue' => 'Central Examination Hall',
                        'notes' => 'End of Semester Final Examination',
                    ]
                );
            }
        }

        // 2. Seed E-Voting Session (Semester 2 Elections)
        $votingSession = VotingSession::updateOrCreate(
            ['title' => 'Guild Student Leaders Elections 2026/2027'],
            [
                'description' => 'Official Annual Guild Student Leadership Elections conducted in Semester 2.',
                'target_semester' => 2,
                'academic_year_id' => $academicYear?->id,
                'start_time' => now()->subDays(1),
                'end_time' => now()->addDays(7),
                'is_active' => true,
            ]
        );

        $pos1 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Guild President'],
            ['description' => 'Head of the Student Guild Representative Council', 'display_order' => 1]
        );

        $pos2 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Vice Guild President'],
            ['description' => 'Deputy Head of the Student Guild Council', 'display_order' => 2]
        );

        $pos3 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Minister of Sports & Recreation'],
            ['description' => 'Oversees student sports, games, and recreational activities', 'display_order' => 3]
        );

        // Candidates
        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos1->id, 'name' => 'Tendo Jeconiah Caleb'],
            [
                'party_affiliation' => 'Progressive Alliance (PA)',
                'manifesto' => 'Transforming student welfare, modernizing campus Wi-Fi, and enhancing academic resources for all software engineering students.',
            ]
        );

        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos1->id, 'name' => 'Akello Mary Patricia'],
            [
                'party_affiliation' => 'Students First Movement',
                'manifesto' => 'Advocating for transparent fees structures, 24/7 library access, and expanded internship placement support.',
            ]
        );

        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos2->id, 'name' => 'Kato Emmanuel'],
            [
                'party_affiliation' => 'Progressive Alliance (PA)',
                'manifesto' => 'Dedicated to student healthcare improvement and mental health wellness programs.',
            ]
        );

        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos3->id, 'name' => 'Mukasa Brian'],
            [
                'party_affiliation' => 'Independent',
                'manifesto' => 'Revitalizing intra-university football, basketball leagues, and e-sports tournaments.',
            ]
        );

        // 3. Seed Evaluation Surveys
        $survey = EvaluationSurvey::updateOrCreate(
            ['title' => 'End of Semester Lecturer Evaluation Survey'],
            [
                'description' => 'Mandatory student evaluation of teaching effectiveness, course delivery, and lecturer engagement.',
                'semester_number' => 1,
                'academic_year_id' => $academicYear?->id,
                'is_active' => true,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(20),
            ]
        );

        $questions = [
            ['question_text' => 'Lecturer demonstrates deep mastery and clarity when explaining complex course concepts.', 'category' => 'Teaching Quality', 'display_order' => 1],
            ['question_text' => 'Lecturer maintains punctuality, attends all scheduled lectures, and utilizes time effectively.', 'category' => 'Punctuality', 'display_order' => 2],
            ['question_text' => 'Course syllabus, slides, assignments, and reading materials were shared promptly.', 'category' => 'Course Materials', 'display_order' => 3],
            ['question_text' => 'Lecturer creates an inclusive learning environment and provides constructive feedback on coursework.', 'category' => 'Engagement', 'display_order' => 4],
            ['question_text' => 'Overall performance rating for this lecturer and course.', 'category' => 'Overall Rating', 'display_order' => 5],
        ];

        foreach ($questions as $q) {
            EvaluationQuestion::updateOrCreate(
                ['survey_id' => $survey->id, 'question_text' => $q['question_text']],
                [
                    'category' => $q['category'],
                    'question_type' => 'rating',
                    'display_order' => $q['display_order'],
                ]
            );
        }
    }
}
