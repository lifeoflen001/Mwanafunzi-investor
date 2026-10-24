<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTwoFactorAuthentication extends Model
{
    protected $fillable = ['user_id', 'secret', 'recovery_codes', 'confirmed_at'];

    protected function casts(): array
    {
        return [
            'secret' => 'encrypted',
            'recovery_codes' => 'encrypted:array',
            'confirmed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enabled(): bool
    {
        return $this->confirmed_at !== null;
    }
}
