<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    public const STATUSES = ['pending', 'approved', 'hidden', 'spam', 'rejected'];

    protected $fillable = ['article_id', 'user_id', 'parent_id', 'name', 'email', 'body', 'status', 'approved_at', 'edited_at'];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime', 'edited_at' => 'datetime'];
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function reports()
    {
        return $this->hasMany(CommentReport::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function displayName(): string
    {
        return $this->user?->name ?: ($this->name ?: 'Mwanafunzi reader');
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->displayName())))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'M';
    }
}
