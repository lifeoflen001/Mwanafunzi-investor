<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'key', 'section_type', 'heading', 'body', 'image', 'cta_label', 'cta_url', 'payload', 'sort_order', 'is_enabled'];

    protected $casts = ['payload' => 'array', 'is_enabled' => 'boolean'];

    public function page() { return $this->belongsTo(Page::class); }
}
