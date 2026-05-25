<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivePost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'users_count' => User::count(),
            'lives_count' => LivePost::count(),
            'admin_users_count' => User::where('is_admin', true)->count(),
        ]);
    }

    public function users(): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'created_at'])
            ->orderByDesc('is_admin')
            ->orderByDesc('id')
            ->get();

        return response()->json($users);
    }

    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ((int) $request->user()->id === (int) $user->id) {
            abort(422, '現在ログイン中の管理者ユーザーは削除できません。');
        }

        if ((bool) $user->is_admin) {
            abort(422, '管理者ユーザーはこの画面から削除できません。');
        }

        $user->delete();

        return response()->json([
            'message' => 'ユーザーを削除しました。',
        ]);
    }

    public function lives(): JsonResponse
    {
        $lives = LivePost::query()
            ->with(['user:id,name,email'])
            ->latest('event_date')
            ->latest('id')
            ->get();

        return response()->json($lives);
    }

    public function updateLive(Request $request, int $id): JsonResponse
    {
        $livePost = LivePost::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'open_time' => ['nullable'],
            'start_time' => ['nullable'],
            'live_house' => ['nullable', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $imagePath = $livePost->image_path;

        if ($request->hasFile('image')) {
            $this->deleteStorageImage($livePost->image_path);

            $path = $request->file('image')->store('live_images', 'public');
            $imagePath = '/storage/' . $path;
        }

        $livePost->update([
            'title' => $validated['title'],
            'event_date' => $validated['event_date'],
            'open_time' => $validated['open_time'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'live_house' => $validated['live_house'] ?? null,
            'artist' => $validated['artist'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return response()->json($livePost->fresh('user:id,name,email'));
    }

    public function deleteLive(int $id): JsonResponse
    {
        $livePost = LivePost::findOrFail($id);

        $this->deleteStorageImage($livePost->image_path);
        $livePost->delete();

        return response()->json([
            'message' => 'ライブ投稿を削除しました。',
        ]);
    }

    private function deleteStorageImage(?string $imagePath): void
    {
        if ($imagePath && str_starts_with($imagePath, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $imagePath);
            Storage::disk('public')->delete($oldPath);
        }
    }
}
