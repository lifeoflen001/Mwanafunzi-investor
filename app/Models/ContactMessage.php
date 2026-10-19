<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'business_unit_id', 'category', 'message', 'consented_at', 'status', 'read_at'];
    protected function casts(): array { return ['consented_at' => 'datetime', 'read_at' => 'datetime']; }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    /** Return a safe single-line preview for inbox and notification surfaces. */
    public function preview(int $limit = 90): string
    {
        $message = str_replace(['\\r\\n', '\\n', '\\r'], ' ', (string) $this->message);

        return Str::limit(Str::squish(strip_tags($message)), $limit);
    }
}
