<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    public const PENDING = 'pending';
    public const ACTIVE = 'active';
    public const UNSUBSCRIBED = 'unsubscribed';
    public const BOUNCED = 'bounced';
    public const SUPPRESSED = 'suppressed';

    public const SOURCES = ['footer', 'blog', 'course', 'landing-page', 'admin'];
    public const STATUSES = [self::PENDING, self::ACTIVE, self::UNSUBSCRIBED, self::BOUNCED, self::SUPPRESSED];

    protected $fillable = [
        'user_id', 'email', 'status', 'source', 'confirmed_at', 'unsubscribed_at', 'last_confirmation_sent_at',
        'confirmation_token_hash', 'unsubscribe_token_hash', 'consent_version', 'consent_ip', 'consent_user_agent',
    ];

    protected $hidden = ['confirmation_token_hash', 'unsubscribe_token_hash', 'consent_ip', 'consent_user_agent'];

    protected function casts(): array
    {
        return ['confirmed_at' => 'datetime', 'unsubscribed_at' => 'datetime', 'last_confirmation_sent_at' => 'datetime', 'unsubscribe_token' => 'encrypted'];
    }

    /**
     * Backfill the encrypted token for legacy/imported rows before lifecycle
     * mail renders an unsubscribe URL.
     */
    public function ensureUnsubscribeToken(): string
    {
        if (filled($this->unsubscribe_token) && filled($this->unsubscribe_token_hash)) {
            return (string) $this->unsubscribe_token;
        }

        $token = Str::random(64);
        $this->forceFill([
            'unsubscribe_token_hash' => hash('sha256', $token),
            'unsubscribe_token' => $token,
        ])->save();

        return $token;
    }

    public function user() { return $this->belongsTo(User::class); }
    public function scopeActive(Builder $query): Builder { return $query->where('status', self::ACTIVE); }
    public function scopePending(Builder $query): Builder { return $query->where('status', self::PENDING); }
    public function scopeUnsubscribed(Builder $query): Builder { return $query->where('status', self::UNSUBSCRIBED); }
}
