<?php

namespace App\Support;

use App\Models\SiteSetting;

final class NewsletterSettings
{
    public static function defaults(): array
    {
        return [
            'enabled' => true,
            'heading' => 'Stay in the loop',
            'description' => 'Receive new articles, Student of Money notes, course updates and useful Mwanafunzi Investor resources.',
            'success_message' => "You're subscribed. Thanks for joining the desk.",
            'pending_message' => 'Thanks — your subscription is pending confirmation. Check your inbox to confirm your email.',
            'confirmation_required' => true,
            'sender_name' => 'Mwanafunzi Investor',
            'sender_email' => 'mwanafunziinvestor@outlook.com',
            'reply_to_email' => 'mwanafunziinvestor@outlook.com',
            'footer_visible' => true,
            'consent_copy' => 'By subscribing, you agree to receive email updates. You can unsubscribe at any time.',
            'consent_version' => '1.0',
            'privacy_url' => '/privacy-policy',
        ];
    }

    public static function all(): array
    {
        return collect(static::defaults())->mapWithKeys(fn ($default, $key) => [$key => SiteSetting::getValue('newsletter.'.$key, $default)])->all();
    }

    public static function save(array $input): void
    {
        foreach (static::defaults() as $key => $default) {
            if (! array_key_exists($key, $input)) continue;
            $value = is_bool($default) ? ($input[$key] ? '1' : '0') : (string) $input[$key];
            SiteSetting::updateOrCreate(['key' => 'newsletter.'.$key], ['value' => $value, 'type' => is_bool($default) ? 'boolean' : 'text']);
        }
        SiteSetting::forgetCache();
    }
}
