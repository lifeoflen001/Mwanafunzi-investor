<?php

namespace App\Support;

use App\Models\BusinessUnit;
use App\Models\NavigationItem;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Cache;

final class PublicSiteData
{
    private const CACHE_KEY = 'public_site.chrome.v1';

    private static ?array $runtimeData = null;

    public static function businessUnits()
    {
        return static::load()['businessUnits'];
    }

    public static function headerNavigation()
    {
        return static::load()['headerNavigation'];
    }

    public static function footerNavigation()
    {
        return static::load()['footerNavigation'];
    }

    public static function socialLinks()
    {
        return static::load()['socialLinks'];
    }

    public static function forgetCache(): void
    {
        static::$runtimeData = null;
        Cache::forget(static::CACHE_KEY);
    }

    private static function load(): array
    {
        if (static::$runtimeData !== null) return static::$runtimeData;

        $loader = fn (): array => [
            'businessUnits' => BusinessUnit::query()->active()->orderBy('sort_order')->get(),
            'headerNavigation' => NavigationItem::with('children')->visible()->where('location', 'header')->whereNull('parent_id')->get(),
            'footerNavigation' => NavigationItem::with('children')->visible()->where('location', 'footer')->whereNull('parent_id')->orderBy('menu_group')->get()->groupBy('menu_group'),
            'socialLinks' => static::uniqueSocialLinks(),
        ];

        static::$runtimeData = app()->environment('testing')
            ? $loader()
            : Cache::remember(static::CACHE_KEY, now()->addMinutes(5), $loader);

        return static::$runtimeData;
    }

    private static function uniqueSocialLinks()
    {
        return SocialLink::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get()
            // Prefer the last configured link when an old and new record use the same platform.
            ->reverse()
            ->unique(fn (SocialLink $link): string => static::socialPlatformKey($link->label))
            ->reverse()
            ->values();
    }

    private static function socialPlatformKey(string $label): string
    {
        $label = strtolower(trim($label));

        return match (true) {
            str_contains($label, 'instagram') => 'instagram',
            str_contains($label, 'facebook') => 'facebook',
            str_contains($label, 'linkedin') => 'linkedin',
            str_contains($label, 'youtube') => 'youtube',
            str_contains($label, 'tiktok') => 'tiktok',
            str_contains($label, 'twitter') || $label === 'x' => 'x',
            default => $label,
        };
    }
}
