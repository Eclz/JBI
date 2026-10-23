<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private static int $actionCounter = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        self::$actionCounter = 0;

        // Register dedicated test routes wrapped in the 'web' middleware group
        Route::middleware('web')->group(function () {
            Route::post('/test-idempotency-web', function (Request $request) {
                self::$actionCounter++;
                return redirect('/dashboard')->with('success', 'Form processed #' . self::$actionCounter);
            });

            Route::post('/test-idempotency-json', function (Request $request) {
                self::$actionCounter++;
                return response()->json([
                    'success' => true,
                    'counter' => self::$actionCounter,
                    'message' => 'Processed',
                ]);
            });

            Route::post('/test-idempotency-validation', function (Request $request) {
                $request->validate([
                    'email' => 'required|email',
                ]);
                self::$actionCounter++;
                return redirect('/dashboard')->with('success', 'Email accepted');
            });
        });
    }

    public function test_duplicate_web_form_submission_is_replayed_and_not_executed_twice(): void
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Test Item',
            '_idempotency_key' => 'unique-test-key-1',
        ];

        // First submission
        $response1 = $this->actingAs($user)->post('/test-idempotency-web', $payload);
        $response1->assertRedirect('/dashboard');
        $this->assertSame(1, self::$actionCounter, 'Action should have run once');

        // Immediate duplicate submission (same key and payload)
        $response2 = $this->actingAs($user)->post('/test-idempotency-web', $payload);
        $response2->assertRedirect('/dashboard');
        $response2->assertHeader('X-Idempotent-Replay', 'true');
        $this->assertSame(1, self::$actionCounter, 'Action should still only have run once');

        // Distinct payload with different key should execute again
        $distinctPayload = [
            'title' => 'Different Item',
            '_idempotency_key' => 'unique-test-key-2',
        ];
        $response3 = $this->actingAs($user)->post('/test-idempotency-web', $distinctPayload);
        $response3->assertRedirect('/dashboard');
        $this->assertSame(2, self::$actionCounter, 'Action should run for distinct submission');
    }

    public function test_duplicate_json_request_is_replayed_without_reexecuting(): void
    {
        $user = User::factory()->create();

        $payload = [
            'item_id' => 42,
            'amount' => 100,
        ];

        // First JSON post
        $response1 = $this->actingAs($user)->postJson('/test-idempotency-json', $payload);
        $response1->assertOk()
            ->assertJson([
                'success' => true,
                'counter' => 1,
            ]);
        $this->assertSame(1, self::$actionCounter);

        // Immediate duplicate JSON post (unkeyed, fingerprint-based)
        $response2 = $this->actingAs($user)->postJson('/test-idempotency-json', $payload);
        $response2->assertOk()
            ->assertHeader('X-Idempotent-Replay', 'true')
            ->assertJson([
                'success' => true,
                'counter' => 1,
            ]);
        $this->assertSame(1, self::$actionCounter, 'JSON action should not execute a second time');
    }

    public function test_validation_errors_are_never_cached_as_replays(): void
    {
        $user = User::factory()->create();

        $invalidPayload = [
            'email' => 'not-an-email',
            '_idempotency_key' => 'validation-key-1',
        ];

        // Submit invalid
        $response1 = $this->actingAs($user)->post('/test-idempotency-validation', $invalidPayload);
        $response1->assertSessionHasErrors(['email']);
        $this->assertSame(0, self::$actionCounter);

        // Submit valid with the same key
        $validPayload = [
            'email' => 'test@example.com',
            '_idempotency_key' => 'validation-key-1',
        ];
        $response2 = $this->actingAs($user)->post('/test-idempotency-validation', $validPayload);
        $response2->assertSessionHasNoErrors();
        $response2->assertRedirect('/dashboard');
        $this->assertSame(1, self::$actionCounter, 'Valid submission should succeed after validation failure');
    }
}
