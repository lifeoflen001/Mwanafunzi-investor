<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = ['label', 'url', 'sort_order', 'is_visible'];
    protected function casts(): array { return ['is_visible' => 'boolean']; }
}
