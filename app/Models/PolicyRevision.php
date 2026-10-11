<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyRevision extends Model
{
    protected $fillable = ['page_id', 'revision', 'page_name', 'title', 'summary', 'effective_date', 'sections', 'changed_by'];

    protected function casts(): array
    {
        return ['effective_date' => 'date', 'sections' => 'array'];
    }

    public function page() { return $this->belongsTo(Page::class); }
    public function editor() { return $this->belongsTo(User::class, 'changed_by'); }
}
