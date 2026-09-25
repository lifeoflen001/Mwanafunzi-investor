<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['location', 'menu_group', 'parent_id', 'label', 'route_name', 'url', 'target', 'cta_style', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order'); }
    public function scopeVisible($query) { return $query->where('is_visible', true)->orderBy('sort_order'); }

    public function href(): string
    {
        if ($this->route_name === 'legal' && $this->url) return route('legal', $this->url);
        if ($this->route_name && \Illuminate\Support\Facades\Route::has($this->route_name)) return route($this->route_name);
        return $this->url ?: '#';
    }
}
