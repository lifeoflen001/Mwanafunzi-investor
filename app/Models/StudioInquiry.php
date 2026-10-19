<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudioInquiry extends Model
{
    public const STATUSES = ['new', 'reviewing', 'quoted', 'accepted', 'declined', 'scheduled', 'in_production', 'editing', 'awaiting_approval', 'delivered', 'completed'];

    protected $fillable = [
        'reference', 'user_id', 'business_unit_id', 'project_id', 'service_id', 'name', 'email', 'phone',
        'preferred_date', 'preferred_time', 'location', 'duration', 'budget_range', 'description',
        'special_requirements', 'reference_files', 'status', 'admin_notes', 'reviewed_at',
        'quoted_at', 'confirmed_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'reference_files' => 'array',
            'reviewed_at' => 'datetime',
            'quoted_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function businessUnit() { return $this->belongsTo(BusinessUnit::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function service() { return $this->belongsTo(Service::class); }

    public function scopeStudio(Builder $query): Builder
    {
        return $query->whereHas('businessUnit', fn (Builder $businessUnits) => $businessUnits->where('slug', 'studio'));
    }

    public function statusLabel(): string
    {
        return str_replace('_', ' ', str($this->status)->title());
    }
}
