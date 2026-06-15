<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'business_name', 'avatar', 'plan', 'google_id', 'facebook_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan' => 'string',
        ];
    }

    // ─── Relationships ───

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
