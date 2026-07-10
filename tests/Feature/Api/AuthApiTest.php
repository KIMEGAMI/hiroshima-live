<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login-user@example.com',
            'password' => Hash::make('secure-password'),
        ]);

        $response = $this->postJson(
            '/api/login',
            [
                'email' => 'login-user@example.com',
                'password' => 'secure-password',
            ],
            [
                'Referer' => config('app.url').'/',
            ],
        );

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', 'login-user@example.com');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'login-user@example.com',
            'password' => Hash::make('secure-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login-user@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_fetch_current_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/user');

        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    }
}
