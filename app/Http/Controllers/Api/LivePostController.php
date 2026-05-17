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

        if ($request->date) {
            $query->whereDate('event_date', $request->date);
        }

        return $query->latest()->get();
    }

    public function show($id)
    {
        return LivePost::findOrFail($id);
    }
}