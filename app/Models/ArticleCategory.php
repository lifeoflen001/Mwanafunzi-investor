<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'seo_title', 'seo_description', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean', 'sort_order' => 'integer']; }
    public function getRouteKeyName() { return 'slug'; }
    public function articles() { return $this->hasMany(Article::class); }
}
