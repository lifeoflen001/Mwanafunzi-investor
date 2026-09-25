<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class RichText
{
    private const ALLOWED_TAGS = '<p><h2><h3><strong><em><ul><ol><li><a><blockquote><code><pre><br>';

    public static function sanitize(?string $value): ?string
    {
        if ($value === null || $value === '') return $value;
        $value = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $value) ?? $value;
        $value = strip_tags($value, self::ALLOWED_TAGS);
        $value = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value) ?? $value;
        $value = preg_replace_callback('/<a\b[^>]*>/i', static function ($match) {
            preg_match('/href\s*=\s*["\']([^"\']+)["\']/i', $match[0], $href);
            $url = $href[1] ?? '';
            if (! preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $url)) return '<a>';
            return '<a href="'.e($url).'">';
        }, $value) ?? $value;
        return $value;
    }

    public static function render(?string $value): HtmlString
    {
        if (! $value) return new HtmlString('');
        $hasMarkup = $value !== strip_tags($value);
        if ($hasMarkup) return new HtmlString((string) static::sanitize($value));

        // Some seeded/imported CMS records contain escaped newline sequences.
        // Normalize them before escaping so editors see the intended paragraph breaks.
        $value = str_replace(['\\r\\n', '\\n', '\\r'], "\n", $value);
        return new HtmlString(nl2br(e($value)));
    }
}
