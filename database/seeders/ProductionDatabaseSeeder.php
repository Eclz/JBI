<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BasicAcademicSetupSeeder::class);

        User::updateOrCreate(
            ['email' => 'admin@jbiuniversity.com'],
            [
                'name' => 'JBI University Administrator',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'employee_id' => 'JBI001',
                'is_active' => true,
                'email_verified_at' => now(),
                'must_change_password' => true,
                'phone' => '+27 68 443 8415',
                'address' => 'South Africa',
            ]
        );

        foreach ([
            'institution_name' => 'JBI University',
            'institution_address' => 'South Africa',
            'institution_phone' => '+27 68 443 8415',
            'institution_email' => 'info@jbiuniversity.com',
            'default_timezone' => 'Africa/Johannesburg',
            'timezone' => 'Africa/Johannesburg',
            'operating_region' => 'southern_africa',
            'default_currency' => 'ZAR',
            'accepted_currencies' => json_encode(['ZAR', 'USD', 'EUR', 'GBP']),
            'registration_enabled' => 'false',
            'registration_open_at' => '',
            'registration_close_at' => '',
            'demo_mode' => 'false',
        ] as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
        }

        $this->command?->info('Production database initialized with essential academic structure and one administrator.');
    }
}
