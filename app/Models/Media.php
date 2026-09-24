<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['disk', 'path', 'filename', 'mime_type', 'size', 'width', 'height', 'alt_text', 'title', 'variants'];
    protected function casts(): array { return ['variants' => 'array']; }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }
}
