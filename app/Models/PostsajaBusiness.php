<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsajaBusiness extends Model
{
    protected $table = 'postsaja_businesses';

    protected $fillable = [
        'business_name',
        'owner_wa',
        'business_code',
        'telegram_bot_enabled',
    ];

    // ─── Relationships ───

    public function users()
    {
        return $this->belongsToMany(User::class, 'postsaja_business_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /** Users with 'owner' role for this business */
    public function owners()
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /** Users with 'supervisor' role */
    public function supervisors()
    {
        return $this->users()->wherePivot('role', 'supervisor');
    }

    /** Users with 'staff' role */
    public function staffUsers()
    {
        return $this->users()->wherePivot('role', 'staff');
    }

    /** Telegram-based staff (non-app users who only upload via Telegram) */
    public function staff()
    {
        return $this->hasMany(PostsajaStaffTelegram::class, 'business_id');
    }

    public function posts()
    {
        return $this->hasMany(PostsajaPost::class, 'business_id');
    }

    /** All connected social media accounts */
    public function socialAccounts()
    {
        return $this->hasMany(PostsajaSocialAccount::class, 'business_id');
    }

    /** Get social accounts by platform */
    public function socialAccount(string $platform)
    {
        return $this->socialAccounts()->where('platform', $platform)->first();
    }

    // ─── Helpers ───

    public function hasGoogleBusiness(): bool
    {
        return $this->socialAccounts()->where('platform', 'google_business')->where('active', true)->exists();
    }

    public function hasWhatsApp(): bool
    {
        return $this->socialAccounts()->where('platform', 'whatsapp')->where('active', true)->exists();
    }
}
