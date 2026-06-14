<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Business Relationships ───

    /** All businesses this user is associated with (any role) */
    public function businesses()
    {
        return $this->belongsToMany(PostsajaBusiness::class, 'postsaja_business_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /** Businesses where this user is owner */
    public function ownedBusinesses()
    {
        return $this->businesses()->wherePivot('role', 'owner');
    }

    /** Businesses where this user is supervisor */
    public function supervisedBusinesses()
    {
        return $this->businesses()->wherePivot('role', 'supervisor');
    }

    /** Businesses where this user is staff */
    public function staffBusinesses()
    {
        return $this->businesses()->wherePivot('role', 'staff');
    }

    // ─── Filament Admin Access ───

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        return true;
    }
}
