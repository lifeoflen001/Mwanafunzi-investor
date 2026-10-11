<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $settings = [
            ['key' => 'newsletter.enabled', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'newsletter.heading', 'value' => 'Stay in the loop', 'type' => 'text'],
            ['key' => 'newsletter.description', 'value' => 'Receive new articles, Student of Money notes, course updates and useful Mwanafunzi Investor resources.', 'type' => 'text'],
            ['key' => 'newsletter.success_message', 'value' => "You're subscribed. Check your inbox for the next step.", 'type' => 'text'],
            ['key' => 'newsletter.confirmation_required', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'newsletter.sender_name', 'value' => 'Mwanafunzi Investor', 'type' => 'text'],
            ['key' => 'newsletter.sender_email', 'value' => 'mwanafunziinvestor@outlook.com', 'type' => 'text'],
            ['key' => 'newsletter.reply_to_email', 'value' => 'mwanafunziinvestor@outlook.com', 'type' => 'text'],
            ['key' => 'newsletter.footer_visible', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'newsletter.consent_copy', 'value' => 'By subscribing, you agree to receive email updates. You can unsubscribe at any time.', 'type' => 'text'],
            ['key' => 'newsletter.consent_version', 'value' => '1.0', 'type' => 'text'],
            ['key' => 'newsletter.privacy_url', 'value' => '/privacy-policy', 'type' => 'text'],
            ['key' => 'privacy.cookie.enabled', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'privacy.cookie.banner_heading', 'value' => 'Your cookie choices', 'type' => 'text'],
            ['key' => 'privacy.cookie.banner_description', 'value' => 'We use essential cookies to keep Mwanafunzi Investor working. With your permission, we may also use optional cookies for analytics, preferences and other enabled services.', 'type' => 'text'],
            ['key' => 'privacy.cookie.accept_all_label', 'value' => 'Accept all', 'type' => 'text'],
            ['key' => 'privacy.cookie.reject_optional_label', 'value' => 'Reject optional', 'type' => 'text'],
            ['key' => 'privacy.cookie.manage_preferences_label', 'value' => 'Manage preferences', 'type' => 'text'],
            ['key' => 'privacy.cookie.modal_heading', 'value' => 'Cookie Preferences', 'type' => 'text'],
            ['key' => 'privacy.cookie.lifetime_months', 'value' => '9', 'type' => 'integer'],
            ['key' => 'privacy.cookie.version', 'value' => '1.0', 'type' => 'text'],
            ['key' => 'privacy.cookie.policy_url', 'value' => '/cookie-policy', 'type' => 'text'],
            ['key' => 'privacy.cookie.privacy_url', 'value' => '/privacy-policy', 'type' => 'text'],
            ['key' => 'privacy.cookie.preferences_enabled', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'privacy.cookie.analytics_enabled', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'privacy.cookie.marketing_enabled', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'privacy.cookie.essential_description', 'value' => 'Required for sessions, authentication, security and protected form submissions.', 'type' => 'text'],
            ['key' => 'privacy.cookie.preferences_description', 'value' => 'Remembers optional choices that improve your experience.', 'type' => 'text'],
            ['key' => 'privacy.cookie.analytics_description', 'value' => 'Helps us understand aggregate site usage when an analytics service is enabled.', 'type' => 'text'],
            ['key' => 'privacy.cookie.marketing_description', 'value' => 'Supports advertising or marketing measurement when a service is enabled.', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(['key' => $setting['key']], array_merge($setting, ['created_at' => $now, 'updated_at' => $now]));
        }

        $pageId = DB::table('pages')->where('key', 'cookie-policy')->value('id');
        if (! $pageId) {
            $pageId = DB::table('pages')->insertGetId([
                'key' => 'cookie-policy', 'slug' => 'cookie-policy', 'name' => 'Cookie Policy', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true,
                'hero_eyebrow' => 'Privacy / Cookie policy', 'hero_title' => 'Cookie policy', 'hero_summary' => 'How Mwanafunzi Investor uses cookies and how you can manage optional categories.',
                'seo_title' => 'Cookie Policy — Mwanafunzi Investor', 'seo_description' => 'Learn how Mwanafunzi Investor uses cookies and how to manage cookie preferences.', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $sections = [
            ['key' => 'what-cookies-are', 'heading' => 'What cookies are', 'body' => 'Cookies are small files stored by your browser. They help a website remember a session or a choice. This policy describes the cookies and optional services currently documented for Mwanafunzi Investor.', 'sort_order' => 10],
            ['key' => 'essential-cookies', 'heading' => 'Essential cookies', 'body' => 'Essential cookies support sessions, authentication, CSRF protection, security and platform workflows. They cannot be disabled through the preference controls.', 'sort_order' => 20],
            ['key' => 'optional-categories', 'heading' => 'Optional categories', 'body' => 'Preferences, analytics and marketing categories are disabled until you choose them. Optional services are only loaded when the relevant category is enabled and the service is configured.', 'sort_order' => 30],
            ['key' => 'third-party-services', 'heading' => 'Third-party services', 'body' => 'The registry below documents cookies and services that the platform has configured. It is updated from the admin desk as integrations change.', 'sort_order' => 40],
            ['key' => 'managing-preferences', 'heading' => 'Managing preferences', 'body' => 'Use Cookie Preferences in the footer at any time to reopen the controls. Your consent version and selected categories are stored in a consent cookie, not passwords or authentication tokens.', 'sort_order' => 50],
            ['key' => 'retention-and-updates', 'heading' => 'Retention and policy updates', 'body' => 'Consent expires after the configured period. If the consent version changes, the banner will appear again so you can review the updated choices.', 'sort_order' => 60],
        ];
        foreach ($sections as $section) {
            DB::table('page_sections')->updateOrInsert(
                ['page_id' => $pageId, 'key' => $section['key']],
                array_merge($section, ['section_type' => 'rich_text', 'is_enabled' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        DB::table('page_sections')->where('page_id', DB::table('pages')->where('key', 'cookie-policy')->value('id'))->whereIn('key', ['what-cookies-are', 'essential-cookies', 'optional-categories', 'third-party-services', 'managing-preferences', 'retention-and-updates'])->delete();
        DB::table('pages')->where('key', 'cookie-policy')->delete();
        DB::table('site_settings')->whereIn('key', [
            'newsletter.enabled', 'newsletter.heading', 'newsletter.description', 'newsletter.success_message', 'newsletter.confirmation_required', 'newsletter.sender_name', 'newsletter.sender_email', 'newsletter.reply_to_email', 'newsletter.footer_visible', 'newsletter.consent_copy', 'newsletter.consent_version', 'newsletter.privacy_url',
            'privacy.cookie.enabled', 'privacy.cookie.banner_heading', 'privacy.cookie.banner_description', 'privacy.cookie.accept_all_label', 'privacy.cookie.reject_optional_label', 'privacy.cookie.manage_preferences_label', 'privacy.cookie.modal_heading', 'privacy.cookie.lifetime_months', 'privacy.cookie.version', 'privacy.cookie.policy_url', 'privacy.cookie.privacy_url', 'privacy.cookie.preferences_enabled', 'privacy.cookie.analytics_enabled', 'privacy.cookie.marketing_enabled', 'privacy.cookie.essential_description', 'privacy.cookie.preferences_description', 'privacy.cookie.analytics_description', 'privacy.cookie.marketing_description',
        ])->delete();
    }
};
