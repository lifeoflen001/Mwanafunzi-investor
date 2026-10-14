<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    public static function siteUrl(?string $path = null): string
    {
        $base = app()->environment(['local', 'testing'])
            ? (string) config('app.url', 'http://localhost')
            : (string) config('seo.site_url');

        $base = rtrim($base, '/');

        if ($path === null || $path === '') return $base;

        return $base.'/'.ltrim($path, '/');
    }

    public static function absolute(?string $url): ?string
    {
        if (blank($url)) return null;
        if (Str::startsWith($url, ['http://', 'https://'])) return $url;

        return static::siteUrl($url);
    }

    public static function canonical(?string $url = null): string
    {
        $url ??= request()->path();

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parts = parse_url($url);
            $path = $parts['path'] ?? '/';
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';

            return static::siteUrl($path).$query;
        }

        return static::siteUrl($url === '/' ? '' : $url);
    }

    public static function robots(?string $requested = null, bool $preview = false): string
    {
        if ($preview) return 'noindex,nofollow,noarchive';
        if (! app()->environment('production') || config('app.debug')) return 'noindex,nofollow,noarchive';

        return filled($requested) ? $requested : 'index,follow';
    }

    public static function description(?string $description, ?string $fallback = null): string
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) ($description ?: $fallback ?: config('seo.default_description'))));

        return Str::limit($value, 300, '');
    }

    public static function title(?string $title, ?string $fallback = null): string
    {
        return trim($title ?: $fallback ?: config('seo.default_title'));
    }

    public static function routePath(string $url): string
    {
        return (string) (parse_url($url, PHP_URL_PATH) ?: '/');
    }

    public static function productionSitemapUrl(): string
    {
        return static::siteUrl('/sitemap.xml');
    }

    public static function keywordMap(): array
    {
        return (array) config('seo.keyword_map', []);
    }

    public static function keywordTargets(?string $pageKey): array
    {
        return $pageKey && isset(static::keywordMap()[$pageKey]) ? static::keywordMap()[$pageKey] : [];
    }

    public static function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => $item['url'] ?? null,
            ])->all(),
        ];
    }
}
