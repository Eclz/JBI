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
use App\Models\ElectoralCommissionMember;
use App\Models\Faculty;
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
        $facultyMember = User::where('role', 'faculty')->first();
        $adminUser = User::where('role', 'admin')->first();
        $studentUser = User::where('role', 'student')->first();
        $firstFaculty = Faculty::first();

        $votingSession = VotingSession::updateOrCreate(
            ['title' => 'Guild Student Leaders & Cabinet Elections 2026/2027'],
            [
                'description' => 'Official Annual Guild Student Leadership & Faculty Representatives Elections for the 2026/2027 Academic Year.',
                'target_semester' => 2,
                'academic_year_id' => $academicYear?->id,
                'status' => 'voting_open',
                'application_start_at' => now()->subDays(14),
                'application_end_at' => now()->subDays(7),
                'vetting_start_at' => now()->subDays(6),
                'vetting_end_at' => now()->subDays(2),
                'start_time' => now()->subDays(1),
                'end_time' => now()->addDays(7),
                'is_active' => true,
                'created_by' => $adminUser?->id,
            ]
        );

        // Commission Members
        if ($adminUser) {
            ElectoralCommissionMember::updateOrCreate(
                ['voting_session_id' => $votingSession->id, 'user_id' => $adminUser->id],
                ['role_title' => 'Electoral Commission Chairperson', 'appointed_at' => now()->subDays(15), 'is_active' => true]
            );
        }
        if ($facultyMember) {
            ElectoralCommissionMember::updateOrCreate(
                ['voting_session_id' => $votingSession->id, 'user_id' => $facultyMember->id],
                ['role_title' => 'Chief Returning Officer', 'appointed_at' => now()->subDays(15), 'is_active' => true]
            );
        }

        // Positions: University-Wide
        $pos1 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Guild President'],
            [
                'description' => 'Head of the Student Guild Representative Council',
                'scope' => 'university_wide',
                'max_votes_per_voter' => 1,
                'display_order' => 1,
                'requirements' => 'Minimum CGPA 3.0, Year 2 or 3, No disciplinary infractions.',
            ]
        );

        $pos2 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Vice Guild President'],
            [
                'description' => 'Deputy Head of the Student Guild Council',
                'scope' => 'university_wide',
                'max_votes_per_voter' => 1,
                'display_order' => 2,
                'requirements' => 'Minimum CGPA 2.8, Year 2 or 3.',
            ]
        );

        $pos3 = VotingPosition::updateOrCreate(
            ['voting_session_id' => $votingSession->id, 'title' => 'Minister of Sports & Social Affairs'],
            [
                'description' => 'Oversees student sports, games, and recreational activities across the university',
                'scope' => 'university_wide',
                'max_votes_per_voter' => 1,
                'display_order' => 3,
                'requirements' => 'Demonstrated sports & leadership experience.',
            ]
        );

        // Position: Faculty Specific (if faculty exists)
        if ($firstFaculty) {
            $pos4 = VotingPosition::updateOrCreate(
                ['voting_session_id' => $votingSession->id, 'title' => 'Faculty Representative Council (FRC) Chairperson - ' . $firstFaculty->name],
                [
                    'description' => 'Represents all students in the Faculty of ' . $firstFaculty->name,
                    'scope' => 'faculty_specific',
                    'faculty_id' => $firstFaculty->id,
                    'max_votes_per_voter' => 1,
                    'display_order' => 4,
                    'requirements' => 'Must be a fully registered student in ' . $firstFaculty->name,
                ]
            );

            VotingCandidate::updateOrCreate(
                ['voting_position_id' => $pos4->id, 'name' => 'Ssemwogerere David'],
                [
                    'voting_session_id' => $votingSession->id,
                    'user_id' => $studentUser?->id,
                    'slogan' => 'Innovation & Faculty Unity',
                    'party_affiliation' => 'Faculty Action Group',
                    'cgpa' => 3.85,
                    'year_of_study' => 2,
                    'faculty_id' => $firstFaculty->id,
                    'application_status' => 'vetted_approved',
                    'candidate_status' => 'approved_candidate',
                    'status' => 'approved',
                    'vetting_score' => 94.0,
                    'vetted_at' => now()->subDays(3),
                    'vetted_by' => $adminUser?->id,
                    'vetting_notes' => 'Exceptional academic standing and clear faculty representation agenda.',
                    'manifesto' => 'Bridging the gap between faculty staff and students, advocating for modern lab equipment and accessible learning resources.',
                ]
            );
        }

        // Candidates for Guild President
        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos1->id, 'name' => 'Tendo Jeconiah Caleb'],
            [
                'voting_session_id' => $votingSession->id,
                'user_id' => $studentUser?->id,
                'slogan' => 'Inclusivity, Innovation & Student Welfare',
                'party_affiliation' => 'Progressive Alliance (PA)',
                'cgpa' => 3.92,
                'year_of_study' => 3,
                'faculty_id' => $firstFaculty?->id,
                'application_status' => 'vetted_approved',
                'candidate_status' => 'approved_candidate',
                'status' => 'approved',
                'vetting_score' => 96.5,
                'vetted_at' => now()->subDays(3),
                'vetted_by' => $adminUser?->id,
                'vetting_notes' => 'Passed all academic and ethical vetting criteria with distinction.',
                'manifesto' => 'Transforming student welfare, modernizing campus Wi-Fi infrastructure, 24/7 library power backup, and enhancing academic resources for all students.',
            ]
        );

        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos1->id, 'name' => 'Akello Mary Patricia'],
            [
                'voting_session_id' => $votingSession->id,
                'slogan' => 'Students First, Action Always',
                'party_affiliation' => 'Students First Movement',
                'cgpa' => 3.78,
                'year_of_study' => 2,
                'faculty_id' => $firstFaculty?->id,
                'application_status' => 'vetted_approved',
                'candidate_status' => 'approved_candidate',
                'status' => 'approved',
                'vetting_score' => 91.0,
                'vetted_at' => now()->subDays(3),
                'vetted_by' => $adminUser?->id,
                'vetting_notes' => 'Vetted and cleared for ballot.',
                'manifesto' => 'Advocating for transparent fees structures, expanded internship placement partnerships, and university healthcare emergency funds.',
            ]
        );

        // Candidates for Vice President
        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos2->id, 'name' => 'Kato Emmanuel'],
            [
                'voting_session_id' => $votingSession->id,
                'slogan' => 'Wellness & Academic Excellence',
                'party_affiliation' => 'Progressive Alliance (PA)',
                'cgpa' => 3.65,
                'year_of_study' => 2,
                'faculty_id' => $firstFaculty?->id,
                'application_status' => 'vetted_approved',
                'candidate_status' => 'approved_candidate',
                'status' => 'approved',
                'vetting_score' => 88.0,
                'vetted_at' => now()->subDays(3),
                'vetted_by' => $adminUser?->id,
                'vetting_notes' => 'Cleared by Electoral Commission.',
                'manifesto' => 'Dedicated to student healthcare improvement, mental health wellness programs, and hostel security.',
            ]
        );

        // Candidates for Sports Minister
        VotingCandidate::updateOrCreate(
            ['voting_position_id' => $pos3->id, 'name' => 'Mukasa Brian'],
            [
                'voting_session_id' => $votingSession->id,
                'slogan' => 'Revitalizing Campus Sports & Talents',
                'party_affiliation' => 'Independent',
                'cgpa' => 3.40,
                'year_of_study' => 2,
                'faculty_id' => $firstFaculty?->id,
                'application_status' => 'vetted_approved',
                'candidate_status' => 'approved_candidate',
                'status' => 'approved',
                'vetting_score' => 89.0,
                'vetted_at' => now()->subDays(3),
                'vetted_by' => $adminUser?->id,
                'vetting_notes' => 'Strong sports background and clear vision.',
                'manifesto' => 'Organizing inter-faculty sports galas, securing sponsorships for university teams, and revitalizing campus recreation facilities.',
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
