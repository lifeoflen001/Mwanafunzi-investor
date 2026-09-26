<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NavigationItem extends Model
{
    use SoftDeletes;
    protected $fillable = ['location', 'menu_group', 'parent_id', 'label', 'route_name', 'url', 'target', 'cta_style', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order'); }
    public function scopeVisible($query) { return $query->where('is_visible', true)->orderBy('sort_order'); }

    public function href(): string
    {
        $routeName = $this->publicRouteName();
        if ($routeName === 'legal' && $this->url) return route('legal', $this->url);
        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) return route($routeName);
        return $this->url ?: '#';
    }

    public function publicRouteName(): ?string
    {
        return match ($this->route_name) {
            'home' => $this->label === 'Forex Academy' ? 'forex-academy' : 'home',
            'development' => 'digital-systems',
            'studio' => 'creative-studio',
            default => $this->route_name,
        };
    }
}
