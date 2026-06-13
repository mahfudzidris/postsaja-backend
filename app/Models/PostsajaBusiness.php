<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsajaBusiness extends Model
{
    protected $fillable = [
        'business_name',
        'owner_name',
        'owner_wa',
        'business_code',
        'telegram_bot_enabled',
        'google_business_token',
        'fb_token',
        'ig_token',
    ];

    public function staff()
    {
        $this->hasMany(PostsajaStaffTelegram::class, 'business_id');
    }

    public function posts()
    {
        return $this->hasMany(PostsajaPost::class, 'business_id');
    }
}
