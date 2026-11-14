<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveSubmission extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CORRECTION_REQUESTED = 'correction_requested';
    public const STATUS_CLAIMED = 'claimed';

    public const APPLICANT_TYPES = [
        'artist_band',
        'live_house',
        'organizer',
        'student',
        'dj_idol',
        'other',
    ];

    protected $fillable = [
        'reference_number',
        'status',
        'user_id',
        'live_post_id',
        'applicant_name',
        'organization_name',
        'applicant_type',
        'contact_email',
        'phone',
        'official_site_url',
        'instagram_url',
        'x_url',
        'event_title',
        'event_date',
        'open_time',
        'start_time',
        'venue_name',
        'venue_address',
        'performers',
        'description',
        'ticket_price',
        'ticket_url',
        'notes',
        'flyer_image_path',
        'official_event_url',
        'submitted_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livePost()
    {
        return $this->belongsTo(LivePost::class);
    }
}
