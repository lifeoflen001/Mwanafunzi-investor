<?php

namespace App\Support;

use App\Models\SiteSetting;

final class BrandAssets
{
    /**
     * Resolve an admin-selected logo and fall back to the approved bundled asset.
     * Unpublished or missing media is never rendered as a broken public image.
     */
    public static function logo(string $variant): string
    {
        $fallback = match ($variant) {
            'light' => 'images/brand/mwanafunzi-logo-light.webp',
            default => 'images/brand/mwanafunzi-logo-dark.webp',
        };
        $configured = SiteSetting::getValue($variant.'_logo') ?: SiteSetting::getValue('logo');
        $candidate = filled($configured) ? PublicHero::candidate((string) $configured) : null;

        return $candidate['url'] ?? asset($fallback);
    }
}
