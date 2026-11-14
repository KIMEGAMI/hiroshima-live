<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LivePost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'event_date',
        'open_time',
        'start_time',
        'live_house',
        'artist',
        'description',
        'image_path',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'live_post_tag')
            ->withTimestamps();
    }
}
