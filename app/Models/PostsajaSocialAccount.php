<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsajaSocialAccount extends Model
{
    protected $table = 'postsaja_social_accounts';

    protected $fillable = [
        'business_id',
        'platform',
        'label',
        'token',
        'meta',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'meta' => 'array',
            'active' => 'boolean',
        ];
    }

    public function business()
    {
        return $this->belongsTo(PostsajaBusiness::class, 'business_id');
    }

    // ─── Platform Helpers ───

    public function isGoogleBusiness(): bool
    {
        return $this->platform === 'google_business';
    }

    public function isWhatsApp(): bool
    {
        return $this->platform === 'whatsapp';
    }

    /** Decode Google token into structured data */
    public function googleToken(): ?array
    {
        if (!$this->isGoogleBusiness() || !$this->token) {
            return null;
        }

        return json_decode(decrypt($this->token), true);
    }

    /** Get WhatsApp provider config from meta */
    public function whatsappConfig(): ?array
    {
        if (!$this->isWhatsApp()) {
            return null;
        }

        return $this->meta;
    }
}
