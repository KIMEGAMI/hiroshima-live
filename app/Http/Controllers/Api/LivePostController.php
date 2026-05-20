<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivePost;
use Illuminate\Http\Request;

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

    public function store(Request $request)
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
}