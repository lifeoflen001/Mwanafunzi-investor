<?php

namespace App\Support;

use App\Models\PageSection;

/**
 * Reads optional section payload values without confusing an omitted value
 * with an explicitly empty value. This lets editors intentionally clear
 * optional copy instead of having Blade fall back to stale template text.
 */
final class CmsContent
{
    public static function value(?PageSection $section, string $key, mixed $fallback = null): mixed
    {
        if (! $section) {
            return $fallback;
        }

        $payload = $section->payload ?: [];

        return array_key_exists($key, $payload) ? $payload[$key] : $fallback;
    }

    public static function bool(?PageSection $section, string $key, bool $fallback = false): bool
    {
        return (bool) static::value($section, $key, $fallback);
    }
}
