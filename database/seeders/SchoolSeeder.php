<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Department;
use App\Models\User;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dean = User::where('role', 'faculty')->first() ?? User::where('role', 'admin')->first();
        $deanId = $dean?->id;

        $schoolsData = [
            [
                'name' => 'School of Theology & Ministry',
                'code' => 'STM',
                'description' => 'Dedicated to ministerial studies, biblical research, and pastoral leadership excellence.',
                'location' => 'Main Campus, Building A',
                'phone' => '+256 414 100 001',
                'email' => 'stm@jbiuniversity.com',
            ],
            [
                'name' => 'School of Leadership & Management',
                'code' => 'SLM',
                'description' => 'Empowering transformative organizational leaders and public sector administrators.',
                'location' => 'Main Campus, Building B',
                'phone' => '+256 414 100 002',
                'email' => 'slm@jbiuniversity.com',
            ],
            [
                'name' => 'School of Strategy & Innovation',
                'code' => 'SSI',
                'description' => 'Fostering strategic foresight, corporate innovation, and entrepreneurial ventures.',
                'location' => 'Innovation Complex, Level 2',
                'phone' => '+256 414 100 003',
                'email' => 'ssi@jbiuniversity.com',
            ],
            [
                'name' => 'School of Business',
                'code' => 'SOB',
                'description' => 'Pioneering business analytics, financial management, accounting, and marketing.',
                'location' => 'Business Center, Level 1',
                'phone' => '+256 414 100 004',
                'email' => 'sob@jbiuniversity.com',
            ],
            [
                'name' => 'School of Technology',
                'code' => 'SOT',
                'description' => 'Advancing computer science, software engineering, cybersecurity, and artificial intelligence.',
                'location' => 'Tech Hub, Block C',
                'phone' => '+256 414 100 005',
                'email' => 'sot@jbiuniversity.com',
            ],
            [
                'name' => 'School of Engineering',
                'code' => 'SOE',
                'description' => 'Leading innovation in electrical, civil, mechanical, and systems engineering.',
                'location' => 'Engineering Complex',
                'phone' => '+256 414 100 006',
                'email' => 'soe@jbiuniversity.com',
            ],
            [
                'name' => 'School of Science',
                'code' => 'SOS',
                'description' => 'Exploring fundamental scientific principles, biotechnology, and environmental science.',
                'location' => 'Science Quadrangle',
                'phone' => '+256 414 100 007',
                'email' => 'sos@jbiuniversity.com',
            ],
            [
                'name' => 'School of Music & Media',
                'code' => 'SMM',
                'description' => 'Cultivating creative expression, digital media production, journalism, and musical arts.',
                'location' => 'Arts & Media Studio',
                'phone' => '+256 414 100 008',
                'email' => 'smm@jbiuniversity.com',
            ],
        ];

        foreach ($schoolsData as $data) {
            $school = School::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, [
                    'dean_id' => $deanId,
                    'is_active' => true,
                ])
            );
        }

        // Link existing departments to School of Technology / School of Business by default
        $techSchool = School::where('code', 'SOT')->first();
        if ($techSchool) {
            Department::whereNull('school_id')->update(['school_id' => $techSchool->id]);
        }
    }
}
