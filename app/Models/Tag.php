<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'description'];
    public function getRouteKeyName() { return 'slug'; }
    public function articles() { return $this->belongsToMany(Article::class); }
}
