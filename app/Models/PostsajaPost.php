<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsajaPost extends Model
{
    protected $table = 'postsaja_posts';

    protected $fillable = [
        'business_id',
        'staff_chat_id',
        'image_url',
        'ai_caption',
        'platforms_posted',
        'status',
        'analytics',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'platforms_posted' => 'array',
            'analytics' => 'array',
        ];
    }

    public function business()
    {
        return $this->belongsTo(PostsajaBusiness::class, 'business_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Scopes ───

    /** Posts waiting for supervisor approval */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /** Posts that have been posted */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }
}
