<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = 'https://hiroshima-live.shinji.work';

        $staticUrls = [
            [
                'loc' => $baseUrl . '/',
                'lastmod' => now()->toDateString(),
                'priority' => '1.0',
            ],
            [
                'loc' => $baseUrl . '/lives',
                'lastmod' => now()->toDateString(),
                'priority' => '0.9',
            ],
        ];

        $liveUrls = DB::table('live_posts')
            ->select('id', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($live) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/lives/' . $live->id,
                    'lastmod' => $live->updated_at
                        ? date('Y-m-d', strtotime($live->updated_at))
                        : now()->toDateString(),
                    'priority' => '0.8',
                ];
            })
            ->toArray();

        $urls = array_merge($staticUrls, $liveUrls);

        $xml = view('sitemap', [
            'urls' => $urls,
        ])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}