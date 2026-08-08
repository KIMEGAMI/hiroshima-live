<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if (in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER], true)) {
            return response()->json([
                'message' => '登録済みのメールアドレスの場合、パスワード再設定メールを送信しました。',
            ]);
        }

        return response()->json(['message' => 'しばらく時間をおいてから再度お試しください。'], 422);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'パスワードを変更しました。'])
            : response()->json(['message' => 'パスワード変更に失敗しました。'], 422);
    }
}
