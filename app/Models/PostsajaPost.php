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
}
