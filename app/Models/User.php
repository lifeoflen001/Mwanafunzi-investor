<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone',
        'country',
        'job_title',
        'department',
        'bio',
        'avatar_path',
        'status',
        'last_login_at',
        'admin_mfa_secret',
        'admin_mfa_enabled',
        'admin_mfa_recovery_codes',
        'admin_mfa_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'admin_mfa_secret',
        'admin_mfa_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'admin_mfa_secret' => 'encrypted',
            'admin_mfa_enabled' => 'boolean',
            'admin_mfa_recovery_codes' => 'encrypted:array',
            'admin_mfa_confirmed_at' => 'datetime',
        ];
    }

    public function orders() { return $this->hasMany(Order::class); }
    public function entitlements() { return $this->hasMany(Entitlement::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function waitlists() { return $this->hasMany(CourseWaitlist::class); }
    public function articleReactions() { return $this->hasMany(ArticleReaction::class); }
    public function comments() { return $this->hasMany(Comment::class); }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'A';
    }
}
