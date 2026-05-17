<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivePostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('live_posts')->insert([
            [
                'title' => 'Hiroshima Rock Night',
                'event_date' => '2026-06-15',
                'open_time' => '18:00:00',
                'start_time' => '18:30:00',
                'live_house' => '広島 CLUB QUATTRO',
                'artist' => 'Hiroshima Band / Red Noise',
                'description' => '広島中心部で開催されるロックイベントです。',
                'image_path' => '/images/hiroshima.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Indie Music Session',
                'event_date' => '2026-06-21',
                'open_time' => '17:30:00',
                'start_time' => '18:00:00',
                'live_house' => 'Live Space Reed',
                'artist' => 'Blue Echo / Night Walkers',
                'description' => 'インディーズアーティスト中心のライブイベントです。',
                'image_path' => '/images/hiroshima.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Acoustic Night',
                'event_date' => '2026-06-28',
                'open_time' => '19:00:00',
                'start_time' => '19:30:00',
                'live_house' => '広島市内ライブバー',
                'artist' => 'Mizuki / Sora',
                'description' => 'アコースティック中心の落ち着いたライブです。',
                'image_path' => '/images/hiroshima.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}