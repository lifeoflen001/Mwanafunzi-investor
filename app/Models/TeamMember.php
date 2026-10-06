<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'role', 'department', 'short_intro', 'bio', 'focus', 'expertise',
        'portrait', 'portrait_focal_point', 'linkedin_url', 'instagram_url', 'x_url',
        'facebook_url', 'youtube_url', 'tiktok_url', 'github_url', 'website_url',
        'public_email', 'sort_order', 'is_active', 'is_featured', 'seo_title',
        'seo_description', 'canonical_url', 'robots', 'og_image',
    ];

    protected function casts(): array
    {
        return [
            'expertise' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName() { return 'slug'; }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'team_member_id');
    }

    public function socialLinks(): array
    {
        $links = [
            ['key' => 'linkedin', 'label' => 'LinkedIn', 'short' => 'in', 'url' => $this->linkedin_url],
            ['key' => 'instagram', 'label' => 'Instagram', 'short' => 'ig', 'url' => $this->instagram_url],
            ['key' => 'x', 'label' => 'X', 'short' => 'X', 'url' => $this->x_url],
            ['key' => 'facebook', 'label' => 'Facebook', 'short' => 'f', 'url' => $this->facebook_url],
            ['key' => 'youtube', 'label' => 'YouTube', 'short' => 'yt', 'url' => $this->youtube_url],
            ['key' => 'tiktok', 'label' => 'TikTok', 'short' => 'tk', 'url' => $this->tiktok_url],
            ['key' => 'github', 'label' => 'GitHub', 'short' => 'gh', 'url' => $this->github_url],
            ['key' => 'website', 'label' => 'Website', 'short' => '↗', 'url' => $this->website_url],
        ];

        if ($this->public_email) {
            $links[] = ['key' => 'email', 'label' => 'Email', 'short' => '@', 'url' => 'mailto:'.$this->public_email];
        }

        return collect($links)->filter(fn (array $link) => filled($link['url']))->values()->all();
    }
}
