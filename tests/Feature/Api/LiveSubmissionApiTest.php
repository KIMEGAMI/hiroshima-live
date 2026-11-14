<?php

namespace Tests\Feature\Api;

use App\Models\LiveSubmission;
use App\Notifications\LiveSubmissionReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LiveSubmissionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_live_submission(): void
    {
        Notification::fake();
        Storage::fake('public');

        $image = UploadedFile::fake()->image('flyer.webp');

        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'flyer_image' => $image,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'ライブ情報の掲載申請を受け付けました。')
            ->assertJsonPath('status', LiveSubmission::STATUS_PENDING)
            ->assertJsonStructure(['reference_number']);

        $submission = LiveSubmission::firstOrFail();

        $this->assertSame(LiveSubmission::STATUS_PENDING, $submission->status);
        $this->assertStringStartsWith('HL-', $submission->reference_number);
        $this->assertNotNull($submission->flyer_image_path);
        $this->assertDatabaseHas('live_submissions', [
            'event_title' => '広島インディーズナイト',
            'contact_email' => 'submitter@example.com',
        ]);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $submission->flyer_image_path));
        Notification::assertSentOnDemand(LiveSubmissionReceived::class);
    }

    public function test_live_submission_requires_required_fields(): void
    {
        $response = $this->postJson('/api/live-submissions', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'applicant_name',
                'organization_name',
                'applicant_type',
                'contact_email',
                'event_title',
                'event_date',
                'start_time',
                'venue_name',
                'performers',
                'description',
            ]);
    }

    public function test_live_submission_rejects_invalid_email_and_urls(): void
    {
        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'contact_email' => 'invalid-email',
            'official_site_url' => 'not-url',
            'instagram_url' => 'instagram',
            'x_url' => 'x',
            'ticket_url' => 'ticket',
            'official_event_url' => 'event',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'contact_email',
                'official_site_url',
                'instagram_url',
                'x_url',
                'ticket_url',
                'official_event_url',
            ]);
    }

    public function test_live_submission_rejects_past_event_date(): void
    {
        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'event_date' => now()->subDay()->toDateString(),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('event_date');
    }

    public function test_live_submission_rejects_invalid_file_type(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'flyer_image' => UploadedFile::fake()->create('flyer.pdf', 100, 'application/pdf'),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('flyer_image');
    }

    public function test_live_submission_rejects_oversized_image(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'flyer_image' => UploadedFile::fake()->image('flyer.jpg')->size(10241),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('flyer_image');
    }

    public function test_live_submission_strips_html_tags_before_saving(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/live-submissions', [
            ...$this->validPayload(),
            'event_title' => '<script>alert(1)</script>広島ライブ',
            'description' => '<b>熱い夜</b><script>alert(1)</script>',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('live_submissions', [
            'event_title' => '広島ライブ',
            'description' => '熱い夜',
        ]);
    }

    private function validPayload(): array
    {
        return [
            'applicant_name' => '申請 太郎',
            'organization_name' => '広島インディーズ企画',
            'applicant_type' => 'organizer',
            'contact_email' => 'submitter@example.com',
            'phone' => '082-000-0000',
            'official_site_url' => 'https://example.com',
            'instagram_url' => 'https://www.instagram.com/example',
            'x_url' => 'https://x.com/example',
            'event_title' => '広島インディーズナイト',
            'event_date' => now()->addMonth()->toDateString(),
            'open_time' => '18:00',
            'start_time' => '18:30',
            'venue_name' => '広島ライブハウス',
            'venue_address' => '広島県広島市中区',
            'performers' => 'バンドA / バンドB',
            'description' => '広島のバンドが集まるライブイベントです。',
            'ticket_price' => '前売 2500円',
            'ticket_url' => 'https://example.com/tickets',
            'notes' => '未就学児入場不可',
            'official_event_url' => 'https://example.com/events/hiroshima',
        ];
    }
}
