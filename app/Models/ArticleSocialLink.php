<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleSocialLink extends Model
{
    protected $fillable = ['article_id', 'platform', 'url', 'label', 'thumbnail', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function platformLabel(): string
    {
        return match (strtolower($this->platform)) {
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'x', 'twitter' => 'X',
            'linkedin' => 'LinkedIn',
            default => ucfirst((string) $this->platform),
        };
    }
}
