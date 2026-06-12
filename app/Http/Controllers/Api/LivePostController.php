<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivePost;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivePostController extends Controller
{
    public function index(Request $request)
    {
        $query = LivePost::query()
            ->with('tags');

        if ($request->filled('date')) {
            $query->whereDate('event_date', $request->date);
        }

        if ($request->filled('live_house')) {
            $query->where('live_house', 'like', '%'.$request->live_house.'%');
        }

        if ($request->filled('artist')) {
            $query->where('artist', 'like', '%'.$request->artist.'%');
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($tagQuery) use ($request) {
                $tagQuery->where('tags.name', $request->tag);
            });
        }

        return $query
            ->latest('event_date')
            ->latest('id')
            ->get();
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeCustomTags($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'open_time' => ['nullable'],
            'start_time' => ['nullable'],
            'live_house' => ['nullable', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'custom_tags' => ['nullable', 'array'],
            'custom_tags.*' => ['nullable', 'string', 'max:50'],
        ]);

        $imagePath = '/images/hiroshima.png';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('live_images', 'public');
            $imagePath = '/storage/'.$path;
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

        $this->syncTags($request, $livePost);

        return response()->json($livePost->load('tags'), 201);
    }

    public function show($id)
    {
        return LivePost::query()
            ->with('tags')
            ->findOrFail($id);
    }

    public function myLives(Request $request)
    {
        return LivePost::query()
            ->with('tags')
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

        $this->normalizeCustomTags($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'open_time' => ['nullable'],
            'start_time' => ['nullable'],
            'live_house' => ['nullable', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'custom_tags' => ['nullable', 'array'],
            'custom_tags.*' => ['nullable', 'string', 'max:50'],
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
            $imagePath = '/storage/'.$path;
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

        $this->syncTags($request, $livePost);

        return response()->json($livePost->load('tags'));
    }

    private function syncTags(Request $request, LivePost $livePost): void
    {
        $tagIds = collect($request->input('tag_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $customTagIds = collect($request->input('custom_tags', []))
            ->map(fn ($name) => $this->normalizeTagName($name))
            ->filter()
            ->unique()
            ->map(function ($name) use ($request) {
                return Tag::firstOrCreate(
                    ['name' => $name],
                    [
                        'type' => 'user',
                        'created_by' => $request->user()->id,
                    ]
                )->id;
            });

        $livePost->tags()->sync(
            $tagIds
                ->merge($customTagIds)
                ->unique()
                ->values()
                ->all()
        );
    }

    private function normalizeCustomTags(Request $request): void
    {
        $customTags = collect($request->input('custom_tags', []))
            ->map(fn ($name) => $this->normalizeTagName($name))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $request->merge([
            'custom_tags' => $customTags,
        ]);
    }

    private function normalizeTagName(mixed $value): string
    {
        $name = (string) $value;
        $name = preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u', '', $name);

        return $name ?? '';
    }
}
