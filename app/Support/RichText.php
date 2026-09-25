<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class RichText
{
    private const ALLOWED_TAGS = '<p><h2><h3><strong><em><ul><ol><li><a><blockquote><code><pre><br><table><thead><tbody><tr><th><td><img>';

    public static function sanitize(?string $value): ?string
    {
        if ($value === null || $value === '') return $value;
        $value = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $value) ?? $value;
        $value = strip_tags($value, self::ALLOWED_TAGS);
        $value = preg_replace_callback('/<\s*(\/?)\s*([a-z0-9]+)(?:\s[^>]*)?>/i', static function ($match) {
            $closing = $match[1] === '/';
            $tag = strtolower($match[2]);
            if ($closing) return '</'.$tag.'>';

            if ($tag === 'a') {
                preg_match('/href\s*=\s*["\']([^"\']+)["\']/i', $match[0], $href);
                $url = $href[1] ?? '';
                return preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $url) ? '<a href="'.e($url).'">' : '<a>';
            }

            if ($tag === 'img') {
                preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $match[0], $src);
                preg_match('/alt\s*=\s*["\']([^"\']*)["\']/i', $match[0], $alt);
                $url = $src[1] ?? '';
                if (! preg_match('/^(https?:\/\/|\/|#)/i', $url)) return '';
                return '<img src="'.e($url).'" alt="'.e($alt[1] ?? '').'">';
            }

            if (in_array($tag, ['td', 'th'], true)) {
                preg_match('/colspan\s*=\s*["\']?(\d+)["\']?/i', $match[0], $colspan);
                preg_match('/rowspan\s*=\s*["\']?(\d+)["\']?/i', $match[0], $rowspan);
                $attributes = '';
                if (! empty($colspan[1])) $attributes .= ' colspan="'.min(12, max(1, (int) $colspan[1])).'"';
                if (! empty($rowspan[1])) $attributes .= ' rowspan="'.min(12, max(1, (int) $rowspan[1])).'"';
                return '<'.$tag.$attributes.'>';
            }

            return '<'.$tag.'>';
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
