<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
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

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    // ─── Plan Methods ───

    /**
     * Get the current active plan (from subscription or fallback to plan column).
     */
    public function currentPlan(): ?Plan
    {
        if ($this->subscription && $this->subscription->plan) {
            return $this->subscription->plan;
        }

        // Fallback: return plan matching the user's plan column
        return Plan::where('code', $this->plan)->first() ?? Plan::where('code', 'free')->first();
    }

    /**
     * Get the plan limits for this user.
     */
    public function planLimits(): array
    {
        $plan = $this->currentPlan();

        if (! $plan) {
            return [
                'max_channels' => 1,
                'max_posts_per_month' => 10,
            ];
        }

        return [
            'max_channels' => $plan->max_channels,
            'max_posts_per_month' => $plan->max_posts_per_month,
        ];
    }
}
