<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveSubmission;
use App\Notifications\LiveSubmissionReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LiveSubmissionController extends Controller
{
    private const IMAGE_MAX_KB = 10240;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:100'],
            'organization_name' => ['required', 'string', 'max:150'],
            'applicant_type' => ['required', 'string', Rule::in(LiveSubmission::APPLICANT_TYPES)],
            'contact_email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'official_site_url' => ['nullable', 'url', 'max:2048'],
            'instagram_url' => ['nullable', 'url', 'max:2048'],
            'x_url' => ['nullable', 'url', 'max:2048'],
            'event_title' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'start_time' => ['required', 'date_format:H:i'],
            'venue_name' => ['required', 'string', 'max:150'],
            'venue_address' => ['nullable', 'string', 'max:255'],
            'performers' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:3000'],
            'ticket_price' => ['nullable', 'string', 'max:100'],
            'ticket_url' => ['nullable', 'url', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'flyer_image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:'.self::IMAGE_MAX_KB,
            ],
            'official_event_url' => ['nullable', 'url', 'max:2048'],
        ], $this->messages(), $this->attributes());

        $validated = $this->sanitizeSubmission($validated);
        $imagePath = null;

        if ($request->hasFile('flyer_image')) {
            $path = $request->file('flyer_image')->store('live_submission_images', 'public');
            $imagePath = '/storage/'.$path;
        }

        $submission = LiveSubmission::create([
            ...$validated,
            'reference_number' => $this->generateReferenceNumber(),
            'status' => LiveSubmission::STATUS_PENDING,
            'flyer_image_path' => $imagePath,
            'submitted_at' => now(),
        ]);

        $this->sendReceiptNotification($submission);

        return response()->json([
            'message' => 'ライブ情報の掲載申請を受け付けました。',
            'reference_number' => $submission->reference_number,
            'status' => $submission->status,
        ], 201);
    }

    private function generateReferenceNumber(): string
    {
        do {
            $referenceNumber = 'HL-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (LiveSubmission::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }

    private function sanitizeSubmission(array $validated): array
    {
        $textFields = [
            'applicant_name',
            'organization_name',
            'phone',
            'event_title',
            'venue_name',
            'venue_address',
            'performers',
            'description',
            'ticket_price',
            'notes',
        ];

        foreach ($textFields as $field) {
            if (! array_key_exists($field, $validated) || $validated[$field] === null) {
                continue;
            }

            $value = (string) $validated[$field];
            $value = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $value) ?? '';
            $validated[$field] = trim(strip_tags($value));
        }

        return $validated;
    }

    private function sendReceiptNotification(LiveSubmission $submission): void
    {
        try {
            Notification::route('mail', $submission->contact_email)
                ->notify(new LiveSubmissionReceived($submission));
        } catch (\Throwable) {
            Log::warning('Live submission receipt mail failed.', [
                'submission_reference' => $submission->reference_number,
            ]);
        }
    }

    private function messages(): array
    {
        return [
            'required' => ':attributeは必須です。',
            'email' => ':attributeの形式が正しくありません。',
            'url' => ':attributeのURL形式が正しくありません。',
            'max' => ':attributeが長すぎます。',
            'date' => ':attributeの日付形式が正しくありません。',
            'after_or_equal' => ':attributeは今日以降の日付を入力してください。',
            'date_format' => ':attributeはHH:MM形式で入力してください。',
            'in' => ':attributeの選択値が正しくありません。',
            'mimes' => ':attributeはJPEG、PNG、WebP形式でアップロードしてください。',
            'mimetypes' => ':attributeはJPEG、PNG、WebP形式でアップロードしてください。',
            'file' => ':attributeのアップロードに失敗しました。',
        ];
    }

    private function attributes(): array
    {
        return [
            'applicant_name' => '申請者名',
            'organization_name' => '活動名または団体名',
            'applicant_type' => '申請者種別',
            'contact_email' => '連絡用メールアドレス',
            'phone' => '電話番号',
            'official_site_url' => '公式サイトURL',
            'instagram_url' => 'Instagram URL',
            'x_url' => 'X URL',
            'event_title' => 'イベント名',
            'event_date' => '開催日',
            'open_time' => '開場時間',
            'start_time' => '開演時間',
            'venue_name' => '会場名',
            'venue_address' => '会場住所',
            'performers' => '出演者',
            'description' => 'イベント説明',
            'ticket_price' => 'チケット料金',
            'ticket_url' => 'チケット購入URL',
            'notes' => '年齢制限・注意事項',
            'flyer_image' => 'フライヤー画像',
            'official_event_url' => '公式イベントURL',
        ];
    }
}
