<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:257', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'message' => '登録しました。',
            'user' => $request->user(),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($this->attemptAdminEnvLogin($request, $validated['email'], $validated['password'])) {
            return response()->json([
                'message' => '管理者としてログインしました。',
                'user' => $request->user(),
            ]);
        }

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'message' => 'ログインしました。',
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->save();

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }

    private function attemptAdminEnvLogin(Request $request, string $loginId, string $password): bool
    {
        $adminId = (string) config('services.admin_login.id', '');
        $adminPassword = (string) config('services.admin_login.password', '');

        if ($adminId === '' || $adminPassword === '') {
            return false;
        }

        if (! hash_equals($adminId, $loginId) || ! hash_equals($adminPassword, $password)) {
            return false;
        }

        $adminEmail = (string) config('services.admin_login.email', 'root@hiroshima-live.local');
        $adminName = (string) config('services.admin_login.name', 'root');

        $user = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
            ]
        );

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->save();

        return true;
    }
}
