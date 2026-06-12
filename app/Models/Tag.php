<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'type',
        'created_by',
    ];

    public function livePosts()
    {
        return $this->belongsToMany(LivePost::class, 'live_post_tag')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
