<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'media',
        'channels',
        'status',
        'scheduled_at',
        'analytics',
    ];

    protected function casts(): array
    {
        return [
            'media' => 'array',
            'channels' => 'array',
            'analytics' => 'array',
            'scheduled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
