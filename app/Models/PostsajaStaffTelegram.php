<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsajaStaffTelegram extends Model
{
    protected $table = 'postsaja_staff_telegram';

    protected $fillable = [
        'business_id',
        'telegram_chat_id',
        'telegram_username',
        'display_name',
        'role',
        'active',
    ];

    public function business()
    {
        return $this->belongsTo(PostsajaBusiness::class, 'business_id');
    }
}
