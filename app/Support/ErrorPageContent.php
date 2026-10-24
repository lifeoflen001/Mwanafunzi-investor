<?php

namespace App\Support;

use App\Models\SiteSetting;

final class ErrorPageContent
{
    /**
     * Error-page copy has safe code-owned defaults and optional admin overrides.
     * Keeping the fallback here means an incomplete settings record can never
     * leave an error page without an accessible heading or recovery action.
     */
    public static function defaults(): array
    {
        return [
            '403' => [
                'label' => 'Access denied',
                'title' => 'Access denied',
                'eyebrow' => '403 / Stay within your lane',
                'heading' => 'This space is not open to you.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '404' => [
                'label' => 'Page not found',
                'title' => 'Page not found',
                'eyebrow' => '404 / Return to the process',
                'heading' => 'This page took a wrong turn.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '419' => [
                'label' => 'Session expired',
                'title' => 'Session expired',
                'eyebrow' => '419 / Start a fresh session',
                'heading' => 'That session has expired.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '422' => [
                'label' => 'Request could not be processed',
                'title' => 'Request could not be processed',
                'eyebrow' => '422 / Review the details',
                'heading' => 'A few details need another look.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '429' => [
                'label' => 'Too many requests',
                'title' => 'Too many requests',
                'eyebrow' => '429 / Take a short pause',
                'heading' => 'The process needs a moment.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '500' => [
                'label' => 'Something went wrong',
                'title' => 'Something went wrong',
                'eyebrow' => '500 / Return to the process',
                'heading' => 'Something needs another look.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
            '503' => [
                'label' => 'Back soon',
                'title' => 'Back soon',
                'eyebrow' => 'Maintenance / Study in progress',
                'heading' => 'We are making the process better.',
                'action_label' => 'Return home',
                'footer_copy' => 'Student of Money. Systems. Discipline.',
                'footer_statement' => 'Always a Mwanafunzi.',
            ],
        ];
    }

    public static function fields(): array
    {
        return [
            'title' => ['label' => 'Browser title', 'max' => 190],
            'eyebrow' => ['label' => 'Eyebrow', 'max' => 190],
            'heading' => ['label' => 'Main heading', 'max' => 255],
            'action_label' => ['label' => 'Recovery button label', 'max' => 80],
            'footer_copy' => ['label' => 'Footer copy', 'max' => 190],
            'footer_statement' => ['label' => 'Footer statement', 'max' => 190],
        ];
    }

    public static function codes(): array
    {
        return array_keys(static::defaults());
    }

    public static function for(string $code): array
    {
        $defaults = static::defaults();
        $fallback = $defaults[$code] ?? $defaults['404'];
        $stored = SiteSetting::getValue('error_pages', []);
        $overrides = is_array($stored) && is_array($stored[$code] ?? null) ? $stored[$code] : [];

        return array_merge($fallback, array_intersect_key($overrides, static::fields()));
    }

    public static function all(): array
    {
        return collect(static::codes())->mapWithKeys(fn (string $code) => [$code => static::for($code)])->all();
    }

    public static function filterInput(array $input): array
    {
        return collect(static::codes())->mapWithKeys(function (string $code) use ($input) {
            $values = is_array($input[$code] ?? null) ? $input[$code] : [];
            return [$code => collect(array_keys(static::fields()))
                ->mapWithKeys(fn (string $field) => [$field => trim((string) ($values[$field] ?? static::defaults()[$code][$field]))])
                ->all()];
        })->all();
    }
}
