<?php

namespace App\Support;

use App\Models\SiteSetting;

final class CookieConsentSettings
{
    public static function defaults(): array
    {
        return [
            'enabled' => true, 'banner_heading' => 'Your cookie choices',
            'banner_description' => 'We use essential cookies to keep Mwanafunzi Investor working. With your permission, we may also use optional cookies for analytics, preferences and other enabled services.',
            'accept_all_label' => 'Accept all', 'reject_optional_label' => 'Reject optional', 'manage_preferences_label' => 'Manage preferences',
            'modal_heading' => 'Cookie Preferences', 'lifetime_months' => 9, 'version' => '1.0', 'policy_url' => '/cookie-policy', 'privacy_url' => '/privacy-policy',
            'preferences_enabled' => true, 'analytics_enabled' => false, 'marketing_enabled' => false, 'external_media_enabled' => false,
            'essential_description' => 'Required for sessions, authentication, security and protected form submissions.',
            'preferences_description' => 'Remembers optional choices that improve your experience.',
            'analytics_description' => 'Helps us understand aggregate site usage when an analytics service is enabled.',
            'marketing_description' => 'Supports advertising or marketing measurement when a service is enabled.',
            'external_media_description' => 'Allows content hosted by external platforms to load directly on this site.',
        ];
    }

    public static function all(): array
    {
        return collect(static::defaults())->mapWithKeys(fn ($default, $key) => [$key => SiteSetting::getValue('privacy.cookie.'.$key, $default)])->all();
    }

    public static function save(array $input): void
    {
        foreach (static::defaults() as $key => $default) {
            if (! array_key_exists($key, $input)) continue;
            $value = is_bool($default) ? ($input[$key] ? '1' : '0') : (string) $input[$key];
            SiteSetting::updateOrCreate(['key' => 'privacy.cookie.'.$key], ['value' => $value, 'type' => is_bool($default) ? 'boolean' : 'text']);
        }
        SiteSetting::forgetCache();
    }
}
