<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'category', 'message', 'consented_at', 'status'];
    protected function casts(): array { return ['consented_at' => 'datetime', 'read_at' => 'datetime']; }
}
