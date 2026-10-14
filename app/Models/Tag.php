<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'primary_keyword', 'secondary_keywords', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image'];
    public function getRouteKeyName() { return 'slug'; }
    public function articles() { return $this->belongsToMany(Article::class); }
}
