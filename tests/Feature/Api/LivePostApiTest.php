<?php

namespace Tests\Feature\Api;

use App\Models\LivePost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivePostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_live_post(): void
    {
        $response = $this->postJson('/api/lives', [
            'title' => 'Guest Live',
            'event_date' => '2026-08-15',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('live_posts', [
            'title' => 'Guest Live',
        ]);
    }

    public function test_authenticated_user_can_create_live_post_with_tags(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'rock',
            'type' => 'official',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/lives', [
                'title' => 'Hiroshima Test Live',
                'event_date' => '2026-08-15',
                'open_time' => '18:00',
                'start_time' => '19:00',
                'live_house' => 'SECOND CRUTCH',
                'artist' => 'Test Band',
                'description' => 'CIで投稿作成を検証するライブです。',
                'tag_ids' => [$tag->id],
                'custom_tags' => ['広島', ' 広島 ', 'indie'],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('title', 'Hiroshima Test Live')
            ->assertJsonPath('live_house', 'SECOND CRUTCH')
            ->assertJsonPath('artist', 'Test Band')
            ->assertJsonPath('image_path', '/images/hiroshima.png');

        $livePost = LivePost::query()
            ->where('title', 'Hiroshima Test Live')
            ->firstOrFail();

        $this->assertSame($user->id, $livePost->user_id);
        $this->assertDatabaseHas('tags', ['name' => 'rock']);
        $this->assertDatabaseHas('tags', ['name' => '広島']);
        $this->assertDatabaseHas('tags', ['name' => 'indie']);

        $this->assertSame(
            ['indie', 'rock', '広島'],
            $livePost->tags()->pluck('name')->sort()->values()->all()
        );
    }

    public function test_live_post_requires_title_and_event_date(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/lives', [
                'title' => '',
                'event_date' => '',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'event_date',
            ]);
    }

    public function test_user_can_fetch_only_their_live_posts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        LivePost::create([
            'user_id' => $user->id,
            'title' => 'My Live',
            'event_date' => '2026-08-15',
            'image_path' => '/images/hiroshima.png',
        ]);

        LivePost::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Live',
            'event_date' => '2026-08-16',
            'image_path' => '/images/hiroshima.png',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/my/lives');

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'My Live'])
            ->assertJsonMissing(['title' => 'Other Live']);
    }
}
