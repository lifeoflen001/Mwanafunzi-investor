<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicHero
{
    private static array $preloadedMedia = [];

    public static function preloadMedia(iterable $paths): void
    {
        $paths = collect($paths)->filter()->map(fn ($path) => ltrim((string) $path, '/'))->unique()->values();
        if ($paths->isEmpty()) return;

        $found = Media::query()->whereIn('path', $paths->all())->get()->keyBy('path');
        foreach ($paths as $path) static::$preloadedMedia[$path] = $found->get($path);
    }

    public static function resolve(array $candidates): ?array
    {
        foreach (array_filter($candidates) as $candidate) {
            $resolved = static::candidate((string) $candidate);
            if ($resolved) return $resolved;
        }

        return null;
    }

    public static function candidate(string $path): ?array
    {
        if (Str::startsWith($path, ['https://', 'http://', '//'])) {
            return ['url' => $path, 'srcset' => null, 'position' => 'center center'];
        }

        $path = ltrim($path, '/');
        $isPublicAsset = Str::startsWith($path, ['images/', 'build/', 'favicon.']);
        $exists = $isPublicAsset
            ? is_file(public_path($path))
            : Storage::disk('public')->exists($path);

        if (! $exists) return null;

        $media = null;
        if (! $isPublicAsset) {
            if (! array_key_exists($path, static::$preloadedMedia)) {
                static::$preloadedMedia[$path] = Media::query()->where('path', $path)->first();
            }
            $media = static::$preloadedMedia[$path];
        }
        $variants = $isPublicAsset
            ? collect([480, 768, 1200])->map(fn (int $width) => [
                'width' => $width,
                'path' => preg_replace('/\.webp$/', '-'.$width.'.webp', $path),
            ])->filter(fn (array $variant) => is_file(public_path($variant['path'])))
            : collect($media?->variants ?? []);
        $variants = $variants
            ->filter(fn (array $variant) => ! empty($variant['path']) && ! empty($variant['width']))
            ->sortBy('width');

        return [
            'url' => $isPublicAsset ? asset($path) : asset('storage/'.$path),
            'srcset' => $variants->isEmpty() ? null : $variants->map(fn (array $variant) => ($isPublicAsset ? asset($variant['path']) : asset('storage/'.$variant['path'])).' '.$variant['width'].'w')->implode(', '),
            'position' => 'center center',
            'width' => $media?->width,
            'height' => $media?->height,
        ];
    }
}
