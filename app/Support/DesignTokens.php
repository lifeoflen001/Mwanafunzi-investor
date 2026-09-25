<?php

namespace App\Support;

use App\Models\SiteSetting;

final class DesignTokens
{
    /**
     * These are the safe, intentional design controls exposed to administrators.
     * Layout structure, selectors, breakpoints and interaction logic remain code-owned.
     */
    public static function definitions(): array
    {
        return [
            'ink' => ['label' => 'Ink', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#171817', 'css' => '--ink'],
            'paper' => ['label' => 'Paper', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#f3f0e9', 'css' => '--paper'],
            'paper_deep' => ['label' => 'Paper deep', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#e8e3da', 'css' => '--paper-deep'],
            'muted' => ['label' => 'Muted text', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#72746d', 'css' => '--muted'],
            'line' => ['label' => 'Borders', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#d5d1c8', 'css' => '--line'],
            'copper' => ['label' => 'Primary accent', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#c56c38', 'css' => '--copper'],
            'copper_dark' => ['label' => 'Accent dark', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#91471f', 'css' => '--copper-dark'],
            'night' => ['label' => 'Night surface', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#1c211f', 'css' => '--night'],
            'white' => ['label' => 'White surface', 'group' => 'Brand colours', 'type' => 'color', 'default' => '#fbfaf6', 'css' => '--white'],
            'portal_bg' => ['label' => 'Portal background', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#f4f5f3', 'css' => '--portal-bg'],
            'portal_sidebar' => ['label' => 'Portal sidebar', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#202a27', 'css' => '--portal-sidebar'],
            'portal_blue' => ['label' => 'Portal blue', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#526c9e', 'css' => '--portal-blue'],
            'portal_teal' => ['label' => 'Portal teal', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#0eaa9a', 'css' => '--portal-teal'],
            'portal_gold' => ['label' => 'Portal gold', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#dca943', 'css' => '--portal-gold'],
            'portal_coral' => ['label' => 'Portal coral', 'group' => 'Portal colours', 'type' => 'color', 'default' => '#d56c55', 'css' => '--portal-coral'],
            'container_max_width' => ['label' => 'Public content max width', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 960, 'max' => 1600, 'default' => 1180, 'css' => '--container-max-width', 'unit' => 'px'],
            'page_gutter' => ['label' => 'Public page gutter', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 24, 'max' => 120, 'default' => 80, 'css' => '--page-gutter', 'unit' => 'px'],
            'body_size' => ['label' => 'Base text size', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 14, 'max' => 20, 'default' => 16, 'css' => '--body-size', 'unit' => 'px'],
            'button_height' => ['label' => 'Button height', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 40, 'max' => 72, 'default' => 52, 'css' => '--button-height', 'unit' => 'px'],
            'surface_radius' => ['label' => 'Surface radius', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 0, 'max' => 18, 'default' => 0, 'css' => '--surface-radius', 'unit' => 'px'],
            'admin_radius' => ['label' => 'Admin card radius', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 0, 'max' => 18, 'default' => 4, 'css' => '--admin-radius', 'unit' => 'px'],
            'admin_sidebar_width' => ['label' => 'Admin sidebar width', 'group' => 'Admin layout tokens', 'type' => 'integer', 'min' => 220, 'max' => 320, 'default' => 250, 'css' => '--admin-sidebar-width', 'unit' => 'px'],
            'admin_topbar_height' => ['label' => 'Admin topbar height', 'group' => 'Admin layout tokens', 'type' => 'integer', 'min' => 56, 'max' => 96, 'default' => 72, 'css' => '--admin-topbar-height', 'unit' => 'px'],
            'admin_content_gutter' => ['label' => 'Admin content gutter', 'group' => 'Admin layout tokens', 'type' => 'integer', 'min' => 20, 'max' => 56, 'default' => 32, 'css' => '--admin-content-gutter', 'unit' => 'px'],
            'motion_duration' => ['label' => 'Motion duration', 'group' => 'Layout tokens', 'type' => 'integer', 'min' => 0, 'max' => 800, 'default' => 250, 'css' => '--motion-duration', 'unit' => 'ms'],
        ];
    }

    public static function values(): array
    {
        $definitions = static::definitions();
        $stored = SiteSetting::query()
            ->whereIn('key', collect($definitions)->keys()->map(fn (string $key) => 'design_'.$key))
            ->pluck('value', 'key');

        return collect($definitions)->mapWithKeys(function (array $definition, string $key) use ($stored) {
            $value = $stored->get('design_'.$key, $definition['default']);
            return [$key => static::normalise($key, $value)];
        })->all();
    }

    public static function css(): string
    {
        $definitions = static::definitions();
        $values = static::values();
        $variables = [];

        foreach ($definitions as $key => $definition) {
            $value = $values[$key];
            if (isset($definition['unit'])) $value .= $definition['unit'];
            $variables[] = $definition['css'].':'.$value;
        }

        return ':root{'.implode(';', $variables).';}';
    }

    public static function rules(): array
    {
        return collect(static::definitions())->mapWithKeys(function (array $definition, string $key) {
            $rule = match ($definition['type']) {
                'color' => ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'integer' => ['sometimes', 'required', 'integer', 'min:'.$definition['min'], 'max:'.$definition['max']],
                default => ['sometimes', 'required'],
            };

            return ['design_'.$key => $rule];
        })->all();
    }

    public static function save(array $input): void
    {
        foreach (static::definitions() as $key => $definition) {
            if (! array_key_exists('design_'.$key, $input)) continue;
            $value = static::normalise($key, $input['design_'.$key]);
            SiteSetting::updateOrCreate(
                ['key' => 'design_'.$key],
                ['value' => (string) $value, 'type' => $definition['type'] === 'color' ? 'text' : 'integer'],
            );
        }
    }

    public static function normalise(string $key, mixed $value): string|int
    {
        $definition = static::definitions()[$key];

        if ($definition['type'] === 'color') {
            return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? strtolower((string) $value) : $definition['default'];
        }

        return max($definition['min'], min($definition['max'], (int) $value));
    }
}
