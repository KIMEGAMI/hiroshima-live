<?php

namespace App\Http\Controllers;

use App\Models\LivePost;
use Illuminate\View\View;

class SpaController extends Controller
{
    public function show(?string $any = null): View
    {
        $baseUrl = 'https://hiroshima-live.shinji.work';
        $path = '/' . ltrim($any ?? '', '/');
        $currentUrl = $baseUrl . ($path === '/' ? '/' : $path);
        $defaultImage = $baseUrl . '/favicon.png';

        $seo = [
            'title' => '広島ライブ情報 | hiroshima-live',
            'description' => 'hiroshima-liveは、広島のライブ情報・ライブハウス情報を探せるライブ情報サイトです。',
            'keywords' => '広島ライブ,広島ライブハウス,ライブ情報,音楽イベント,hiroshima-live',
            'robots' => 'index, follow',
            'url' => $currentUrl,
            'image' => $defaultImage,
            'type' => 'website',
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'hiroshima-live',
                'url' => $baseUrl . '/',
                'description' => '広島のライブ情報・ライブハウス情報を探せるライブ情報サイトです。',
                'inLanguage' => 'ja-JP',
            ],
        ];

        if ($path === '/lives') {
            $seo['title'] = 'ライブ一覧 | 広島ライブ情報 | hiroshima-live';
            $seo['description'] = '広島で開催されるライブ情報・音楽イベント情報を一覧で探せます。';
            $seo['url'] = $baseUrl . '/lives';
        }

        if ($path === '/calendar') {
            $seo['title'] = 'ライブカレンダー | 広島ライブ情報 | hiroshima-live';
            $seo['description'] = '広島で開催されるライブ情報をカレンダー形式で探せます。';
            $seo['url'] = $baseUrl . '/calendar';
        }

        if (in_array($path, ['/login', '/register', '/forgot-password', '/reset-password'], true)) {
            $seo['robots'] = 'noindex, nofollow';
        }

        if (preg_match('#^/lives/(\d+)$#', $path, $matches)) {
            $seo = $this->buildLiveSeo((int) $matches[1], $baseUrl, $defaultImage, $seo);
        }

        return view('app', ['seo' => $seo]);
    }

    private function buildLiveSeo(int $id, string $baseUrl, string $defaultImage, array $seo): array
    {
        $live = LivePost::query()
            ->with('tags')
            ->find($id);

        if (! $live) {
            return $seo;
        }

        $image = $live->image_path
            ? (str_starts_with($live->image_path, 'http') ? $live->image_path : $baseUrl . $live->image_path)
            : $defaultImage;

        $description = collect([
            $live->event_date ? $live->event_date->format('Y/m/d') : null,
            $live->live_house,
            $live->artist,
            $live->description,
        ])
            ->filter()
            ->implode(' / ');

        $description = mb_substr($description, 0, 120);
        $startTime = $this->normalizeTime($live->start_time);

        $seo['title'] = $live->title . ' | 広島ライブ情報 | hiroshima-live';
        $seo['description'] = $description ?: '広島のライブ情報をhiroshima-liveで確認できます。';
        $seo['url'] = $baseUrl . '/lives/' . $live->id;
        $seo['image'] = $image;
        $seo['type'] = 'article';
        $seo['structuredData'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $live->title,
            'startDate' => $live->event_date
                ? $live->event_date->format('Y-m-d') . 'T' . ($startTime ?: '00:00:00') . '+09:00'
                : null,
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'image' => [$image],
            'description' => $seo['description'],
            'url' => $seo['url'],
            'location' => [
                '@type' => 'Place',
                'name' => $live->live_house ?: '広島ライブ会場',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressRegion' => '広島県',
                    'addressCountry' => 'JP',
                ],
            ],
            'performer' => [
                '@type' => 'PerformingGroup',
                'name' => $live->artist ?: $live->title,
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'hiroshima-live',
                'url' => $baseUrl . '/',
            ],
        ];

        return $seo;
    }

    private function normalizeTime(?string $time): ?string
    {
        if (! $time || ! preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            return null;
        }

        return strlen($time) === 5 ? $time . ':00' : $time;
    }
}
