<?php

namespace Tests\Feature\Api;

use App\Models\LivePost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_after_accepting_terms_and_privacy_policy(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Register User',
            'email' => 'register-user@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'terms_accepted' => true,
            'privacy_policy_accepted' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', 'register-user@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'register-user@example.com',
        ]);
    }

    public function test_user_cannot_register_without_accepting_terms_and_privacy_policy(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Register User',
            'email' => 'register-user@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'terms_accepted',
                'privacy_policy_accepted',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'register-user@example.com',
        ]);
    }

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

    public function test_guest_cannot_delete_account(): void
    {
        $response = $this->deleteJson('/api/user', [
            'password' => 'password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_delete_their_account_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secure-password'),
        ]);

        $livePost = LivePost::create([
            'user_id' => $user->id,
            'title' => 'Deleted User Live',
            'event_date' => '2026-08-15',
            'image_path' => '/images/hiroshima.png',
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson('/api/user', [
                'password' => 'secure-password',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'アカウントを削除しました。');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        $this->assertDatabaseHas('live_posts', [
            'id' => $livePost->id,
            'user_id' => null,
        ]);
    }

    public function test_account_deletion_requires_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secure-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson('/api/user', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
