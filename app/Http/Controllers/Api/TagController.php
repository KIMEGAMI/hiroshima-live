<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::query()
            ->where('name', '<>', '')
            ->orderByRaw("type = 'admin' desc")
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }
}
