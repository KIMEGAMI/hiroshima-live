<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this
            ->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', '登録済みのメールアドレスの場合、パスワード再設定メールを送信しました。');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_link_points_to_spa_reset_page(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $actionUrl = $notification->toMail($user)->actionUrl;
            $query = parse_url($actionUrl, PHP_URL_QUERY);
            parse_str((string) $query, $parameters);

            $this->assertStringStartsWith(config('app.url').'/reset-password?', $actionUrl);
            $this->assertSame($notification->token, $parameters['token'] ?? null);
            $this->assertSame($user->email, $parameters['email'] ?? null);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $response = $this->postJson('/api/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ]);

            $response
                ->assertOk()
                ->assertJsonPath('message', 'パスワードを変更しました。');

            $this->assertTrue(Hash::check('new-secure-password', $user->refresh()->password));

            return true;
        });
    }
}
