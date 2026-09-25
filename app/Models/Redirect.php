<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['source_path', 'destination_path', 'status_code', 'is_enabled'];
    protected $casts = ['is_enabled' => 'boolean', 'status_code' => 'integer'];
}
