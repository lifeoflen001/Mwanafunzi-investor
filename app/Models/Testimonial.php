<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = ['client_name', 'client_role', 'company', 'testimonial', 'client_photo', 'company_logo', 'project_id', 'rating', 'source_url', 'anonymous_display', 'initials_only', 'hide_company', 'is_featured', 'is_published', 'sort_order', 'testimonial_date'];

    protected function casts(): array { return ['rating' => 'integer', 'anonymous_display' => 'boolean', 'initials_only' => 'boolean', 'hide_company' => 'boolean', 'is_featured' => 'boolean', 'is_published' => 'boolean', 'testimonial_date' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }

    public function scopePublished(Builder $query): Builder { return $query->where('is_published', true); }

    public function displayName(): string
    {
        if ($this->anonymous_display) return 'Client perspective';
        if (! $this->initials_only) return $this->client_name;
        return collect(preg_split('/\s+/', trim($this->client_name)))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
    }
}
