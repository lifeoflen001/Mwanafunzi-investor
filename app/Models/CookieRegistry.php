<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CookieRegistry extends Model
{
    public const CATEGORIES = ['essential', 'preferences', 'analytics', 'marketing', 'external_media'];

    protected $table = 'cookie_registry';
    protected $fillable = ['kind', 'name', 'provider', 'purpose', 'category', 'expiry', 'type', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name'); }
}
