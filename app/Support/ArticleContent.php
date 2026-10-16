<?php

namespace App\Support;

use Illuminate\Support\Str;

class ArticleContent
{
    public static function prepare(?string $content): array
    {
        $html = (string) RichText::render($content);
        $html = preg_replace('/(?<!\S)#[\p{L}\p{N}_-]+/u', '', $html) ?? $html;
        $headings = [];
        $usedIds = [];

        $html = preg_replace_callback('/<h([23])>(.*?)<\/h\1>/is', function (array $match) use (&$headings, &$usedIds): string {
            $label = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
            if ($label === '') {
                return $match[0];
            }

            $baseId = Str::slug($label) ?: 'section';
            $id = $baseId;
            $suffix = 2;
            while (in_array($id, $usedIds, true)) {
                $id = $baseId.'-'.$suffix++;
            }
            $usedIds[] = $id;
            $headings[] = ['level' => (int) $match[1], 'label' => $label, 'id' => $id];

            return '<h'.$match[1].' id="'.$id.'">'.$match[2].'</h'.$match[1].'>';
        }, $html) ?? $html;

        return ['html' => $html, 'toc' => count($headings) >= 2 ? $headings : []];
    }
}
