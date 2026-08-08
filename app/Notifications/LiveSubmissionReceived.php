<?php

namespace App\Notifications;

use App\Models\LiveSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveSubmissionReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly LiveSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('ライブ情報の掲載申請を受け付けました')
            ->greeting($this->submission->applicant_name.' 様')
            ->line('ライブ情報の掲載申請を受け付けました。')
            ->line('申請番号：'.$this->submission->reference_number)
            ->line('管理者が内容を確認後、公開可否をご連絡します。')
            ->line('このメールに心当たりがない場合は、破棄してください。');
    }
}
