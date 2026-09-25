<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'business_unit_id', 'category', 'message', 'consented_at', 'status', 'read_at'];
    protected function casts(): array { return ['consented_at' => 'datetime', 'read_at' => 'datetime']; }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
