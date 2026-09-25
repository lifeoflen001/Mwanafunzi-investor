<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'auditable_type', 'auditable_id', 'summary', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function user() { return $this->belongsTo(User::class); }
}
