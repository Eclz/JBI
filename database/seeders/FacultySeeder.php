<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds for University Faculties.
     */
    public function run(): void
    {
        $dean = User::whereIn('role', ['admin', 'faculty'])->first();
        $deanId = $dean?->id;

        $faculties = [
            [
                'name' => 'Faculty of Theology & Ministry',
                'code' => 'FTM',
                'description' => 'Comprehensive biblical studies, pastoral theology, divinity, and christian ministry training.',
                'location' => 'Theology Complex, Main Campus',
                'email' => 'theology@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 101',
            ],
            [
                'name' => 'Faculty of Leadership & Management',
                'code' => 'FLM',
                'description' => 'Executive leadership development, public policy, governance, and organizational management.',
                'location' => 'Leadership Tower, Floor 3',
                'email' => 'leadership@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 102',
            ],
            [
                'name' => 'Faculty of Strategy & Innovation',
                'code' => 'FSI',
                'description' => 'Strategic planning, corporate innovation, entrepreneurship, and digital transformation.',
                'location' => 'Innovation Hub, Block B',
                'email' => 'innovation@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 103',
            ],
            [
                'name' => 'Faculty of Business',
                'code' => 'FOB',
                'description' => 'Accounting, finance, marketing, procurement, supply chain, and international business.',
                'location' => 'Business School Building',
                'email' => 'business@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 104',
            ],
            [
                'name' => 'Faculty of Technology',
                'code' => 'FOT',
                'description' => 'Computer science, software engineering, artificial intelligence, and cybersecurity.',
                'location' => 'Tech Center, Room 101',
                'email' => 'technology@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 105',
            ],
            [
                'name' => 'Faculty of Engineering',
                'code' => 'FOE',
                'description' => 'Civil engineering, electrical engineering, mechanical engineering, and telecommunications.',
                'location' => 'Engineering Complex, East Wing',
                'email' => 'engineering@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 106',
            ],
            [
                'name' => 'Faculty of Science',
                'code' => 'FOS',
                'description' => 'Mathematics, statistics, physics, chemistry, and biological sciences.',
                'location' => 'Science Laboratories, Building A',
                'email' => 'science@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 107',
            ],
            [
                'name' => 'Faculty of Music & Media',
                'code' => 'FMM',
                'description' => 'Audio production, digital journalism, mass communication, film, and performing arts.',
                'location' => 'Media & Arts Center',
                'email' => 'media@jbiuniversity.ac.ug',
                'phone' => '+256 414 100 108',
            ],
        ];

        foreach ($faculties as $fac) {
            $fac['dean_id'] = $deanId;
            $fac['is_active'] = true;

            $faculty = Faculty::updateOrCreate(['code' => $fac['code']], $fac);

            // Assign departments to faculty by code matching
            if ($fac['code'] === 'FOT') {
                Department::where('code', 'LIKE', '%CS%')
                    ->orWhere('name', 'LIKE', '%Computer%')
                    ->orWhere('name', 'LIKE', '%Software%')
                    ->update(['faculty_id' => $faculty->id]);
            } elseif ($fac['code'] === 'FOB') {
                Department::where('code', 'LIKE', '%BUS%')
                    ->orWhere('name', 'LIKE', '%Business%')
                    ->orWhere('name', 'LIKE', '%Accounting%')
                    ->update(['faculty_id' => $faculty->id]);
            }
        }

        // Map any unassigned department to Faculty of Technology
        $techFaculty = Faculty::where('code', 'FOT')->first();
        if ($techFaculty) {
            Department::whereNull('faculty_id')->update(['faculty_id' => $techFaculty->id]);
        }
    }
}
