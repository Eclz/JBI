<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationWindowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_open_inside_the_configured_window(): void
    {
        $this->setting('registration_enabled', '1', 'boolean');
        $this->setting('timezone', 'Africa/Johannesburg');
        $this->setting('registration_open_at', now()->subHour()->format('Y-m-d H:i:s'));
        $this->setting('registration_close_at', now()->addHour()->format('Y-m-d H:i:s'));

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('Registration is currently closed');
    }

    public function test_registration_page_and_submission_are_closed_outside_the_window(): void
    {
        $this->setting('registration_enabled', '1', 'boolean');
        $this->setting('timezone', 'Africa/Johannesburg');
        $this->setting('registration_open_at', now()->addDay()->format('Y-m-d H:i:s'));
        $this->setting('registration_close_at', now()->addDays(2)->format('Y-m-d H:i:s'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registration is currently closed');

        $this->post(route('register'), [])->assertSessionHasErrors('registration');
    }

    private function setting(string $key, string $value, string $type = 'string'): void
    {
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => 'system']
        );
    }
}
