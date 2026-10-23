<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add type to departments table
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'type')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('type', 30)->default('academic')->after('faculty_id');
                $table->index('type');
            });
        }

        // 2. Add classification to finance_expenses table
        if (Schema::hasTable('finance_expenses') && !Schema::hasColumn('finance_expenses', 'classification')) {
            Schema::table('finance_expenses', function (Blueprint $table) {
                $table->string('classification', 30)->default('academic')->after('category');
                $table->index('classification');
            });
        }

        // 3. Ensure core administrative and operational departments exist
        $now = now();
        $adminAndOpDepts = [
            // Administrative functions
            [
                'name' => 'Office of the Vice-Chancellor & Executive Administration',
                'code' => 'ADM-VC',
                'type' => 'administrative',
                'description' => 'Central university governance, strategic planning, legal and executive leadership.',
                'location' => 'Main Administration Block, 3rd Floor',
                'email' => 'vc.office@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounts Directorate',
                'code' => 'FIN-BUR',
                'type' => 'administrative',
                'description' => 'University bursary, financial planning, expenditure management, revenue, and treasury.',
                'location' => 'Finance Wing, Ground Floor',
                'email' => 'bursar@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources & Talent Management',
                'code' => 'HR-DIR',
                'type' => 'administrative',
                'description' => 'Staff recruitment, employee welfare, payroll administration, and institutional compliance.',
                'location' => 'Main Administration Block, 1st Floor',
                'email' => 'hr@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Registry & Admissions Directorate',
                'code' => 'REG-DIR',
                'type' => 'administrative',
                'description' => 'Student admissions, enrollment governance, academic transcripts, and official registry.',
                'location' => 'Registry Complex, Ground Floor',
                'email' => 'registrar@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'ICT & Digital Infrastructure Directorate',
                'code' => 'ICT-DIR',
                'type' => 'administrative',
                'description' => 'Campus network infrastructure, enterprise software licenses, server administration, and cybersecurity.',
                'location' => 'Technology Centre, 2nd Floor',
                'email' => 'ict.helpdesk@jbi.ac.ug',
                'is_active' => true,
            ],

            // Operational functions
            [
                'name' => 'Estates, Works & Facilities Management',
                'code' => 'OPS-EST',
                'type' => 'operational',
                'description' => 'Campus physical infrastructure, grounds keeping, buildings renovation, utilities (power & water), and repairs.',
                'location' => 'Estates Yard, East Campus',
                'email' => 'estates@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Campus Safety & Security Services',
                'code' => 'OPS-SEC',
                'type' => 'operational',
                'description' => 'Campus security surveillance, 24/7 gate control, emergency response, and safety compliance.',
                'location' => 'Main Gate Security Station',
                'email' => 'security@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Transport, Logistics & Fleet Operations',
                'code' => 'OPS-TRN',
                'type' => 'operational',
                'description' => 'University shuttle buses, staff logistics, vehicle maintenance, and fuel distribution.',
                'location' => 'University Garage & Transport Depot',
                'email' => 'transport@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'Hostels & Student Accommodation Operations',
                'code' => 'OPS-HST',
                'type' => 'operational',
                'description' => 'On-campus residence halls, student welfare, room allocation, and hostel facilities management.',
                'location' => 'Student Affairs Centre',
                'email' => 'hostels@jbi.ac.ug',
                'is_active' => true,
            ],
            [
                'name' => 'University Library Services',
                'code' => 'ACA-LIB',
                'type' => 'academic',
                'description' => 'Main university library, digital research repositories, journal subscriptions, and cataloguing.',
                'location' => 'Central Library Building',
                'email' => 'library@jbi.ac.ug',
                'is_active' => true,
            ],
        ];

        foreach ($adminAndOpDepts as $deptData) {
            $existing = DB::table('departments')->where('code', $deptData['code'])->first();
            if (!$existing) {
                DB::table('departments')->insert(array_merge($deptData, [
                    'faculty_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            } else {
                DB::table('departments')->where('id', $existing->id)->update([
                    'type' => $deptData['type'],
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. Clean up / re-classify any existing expenses that were incorrectly restricted to academic departments
        $estatesDept = DB::table('departments')->where('code', 'OPS-EST')->first();

        // Reclassify utility and internet expenses to operational
        if ($estatesDept) {
            DB::table('finance_expenses')
                ->where('category', 'Utilities & Internet')
                ->update([
                    'classification' => 'operational',
                    'department_id' => $estatesDept->id,
                ]);
        }

        // Reclassify other known expenses
        DB::table('finance_expenses')
            ->whereIn('category', ['Equipment & Repairs', 'Maintenance'])
            ->update(['classification' => 'operational']);

        DB::table('finance_expenses')
            ->whereNull('classification')
            ->orWhere('classification', '')
            ->update(['classification' => 'academic']);
    }

    public function down(): void
    {
        if (Schema::hasTable('finance_expenses') && Schema::hasColumn('finance_expenses', 'classification')) {
            Schema::table('finance_expenses', function (Blueprint $table) {
                $table->dropIndex(['classification']);
                $table->dropColumn('classification');
            });
        }

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'type')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropIndex(['type']);
                $table->dropColumn('type');
            });
        }
    }
};
