<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivePost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivePostController extends Controller
{
    public function index(Request $request)
    {
        $query = LivePost::query();

        if ($request->filled('date')) {
            $query->whereDate('event_date', $request->date);
        }

        return $query
            ->latest('event_date')
            ->latest('id')
            ->get();
    }

    public function store(Request $request): JsonResponse
    {
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

        $imagePath = '/images/hiroshima.png';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('live_images', 'public');
            $imagePath = '/storage/' . $path;
        }

        $livePost = LivePost::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'event_date' => $validated['event_date'],
            'open_time' => $validated['open_time'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'live_house' => $validated['live_house'] ?? null,
            'artist' => $validated['artist'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return response()->json($livePost, 201);
    }

    public function show($id)
    {
        return LivePost::findOrFail($id);
    }

    public function myLives(Request $request)
    {
        return LivePost::query()
            ->where('user_id', $request->user()->id)
            ->latest('event_date')
            ->latest('id')
            ->get();
    }

    public function update(Request $request, $id): JsonResponse
    {
        $livePost = LivePost::findOrFail($id);

        if ((int) $livePost->user_id !== (int) $request->user()->id) {
            abort(403, 'このライブ情報を編集する権限がありません。');
        }

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
            if (
                $livePost->image_path &&
                str_starts_with($livePost->image_path, '/storage/')
            ) {
                $oldPath = str_replace('/storage/', '', $livePost->image_path);
                Storage::disk('public')->delete($oldPath);
            }

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

        return response()->json($livePost);
    }
}