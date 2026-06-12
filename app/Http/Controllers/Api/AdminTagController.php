<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::query()
            ->withCount('livePosts')
            ->orderByRaw("type = 'admin' desc")
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
        ]);

        $tag = Tag::create([
            'name' => trim($validated['name']),
            'type' => 'admin',
            'created_by' => $request->user()->id,
        ]);

        return response()->json($tag, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags', 'name')->ignore($tag->id),
            ],
        ]);

        $tag->update([
            'name' => trim($validated['name']),
        ]);

        return response()->json($tag);
    }

    public function destroy(int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json([
            'message' => 'タグを削除しました。',
        ]);
    }
}
