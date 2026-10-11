@php
    $cookieSettings = \App\Support\CookieConsentSettings::all();
    $resolvePublicUrl = static function (string $path): string {
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        return rtrim(request()->getSchemeAndHttpHost().request()->getBaseUrl(), '/').'/'.ltrim($path, '/');
    };
    $optionalCategories = [
        ['key' => 'preferences', 'label' => 'Preferences / Functional', 'description' => $cookieSettings['preferences_description'], 'enabled' => (bool) $cookieSettings['preferences_enabled']],
        ['key' => 'analytics', 'label' => 'Analytics', 'description' => $cookieSettings['analytics_description'], 'enabled' => (bool) $cookieSettings['analytics_enabled']],
        ['key' => 'marketing', 'label' => 'Marketing', 'description' => $cookieSettings['marketing_description'], 'enabled' => (bool) $cookieSettings['marketing_enabled']],
        ['key' => 'external_media', 'label' => 'External Media', 'description' => $cookieSettings['external_media_description'], 'enabled' => (bool) $cookieSettings['external_media_enabled']],
    ];
@endphp
@if($cookieSettings['enabled'])
    <div class="cookie-consent" data-cookie-consent data-cookie-config="{{ base64_encode(json_encode(['version' => $cookieSettings['version'], 'lifetime' => $cookieSettings['lifetime_months'], 'preferencesEnabled' => $cookieSettings['preferences_enabled'], 'analyticsEnabled' => $cookieSettings['analytics_enabled'], 'marketingEnabled' => $cookieSettings['marketing_enabled'], 'externalMediaEnabled' => $cookieSettings['external_media_enabled']])) }}" hidden>
        <section class="cookie-banner" data-cookie-banner aria-labelledby="cookie-banner-heading">
            <div><p class="eyebrow">Cookies &amp; privacy</p><h2 id="cookie-banner-heading">{{ $cookieSettings['banner_heading'] }}</h2><p>{{ $cookieSettings['banner_description'] }}</p><a href="{{ $resolvePublicUrl($cookieSettings['policy_url']) }}">Read the Cookie Policy</a></div>
            <div class="cookie-banner-actions"><button type="button" class="button button-accent" data-cookie-action="accept">{{ $cookieSettings['accept_all_label'] }}</button><button type="button" class="button button-light" data-cookie-action="reject">{{ $cookieSettings['reject_optional_label'] }}</button><button type="button" class="cookie-text-button" data-cookie-action="manage">{{ $cookieSettings['manage_preferences_label'] }}</button></div>
        </section>
        <div class="cookie-modal" data-cookie-modal role="dialog" aria-modal="true" aria-labelledby="cookie-modal-heading" hidden>
            <div class="cookie-modal-backdrop" data-cookie-action="close"></div>
            <section class="cookie-modal-panel"><div class="cookie-modal-head"><div><p class="eyebrow">Privacy choices</p><h2 id="cookie-modal-heading">{{ $cookieSettings['modal_heading'] }}</h2></div><button type="button" class="cookie-close" aria-label="Close cookie preferences" data-cookie-action="close">×</button></div><p class="cookie-modal-intro">Choose which optional categories you want to allow. Essential cookies remain active because they are required for the website to function securely.</p>
                <div class="cookie-category-list">
                    <div class="cookie-category"><div><strong>Essential Cookies</strong><p>{{ $cookieSettings['essential_description'] }}</p></div><span class="cookie-always-active">Always Active</span></div>
                    @foreach($optionalCategories as $category)
                        @if($category['enabled'])
                            <label class="cookie-category" for="cookie-toggle-{{ $category['key'] }}"><span><strong>{{ $category['label'] }}</strong><p id="cookie-description-{{ $category['key'] }}">{{ $category['description'] }}</p></span><input id="cookie-toggle-{{ $category['key'] }}" class="cookie-toggle-input" type="checkbox" role="switch" aria-label="Allow {{ $category['label'] }}" aria-describedby="cookie-description-{{ $category['key'] }}" data-cookie-toggle="{{ $category['key'] }}"><span class="cookie-toggle" aria-hidden="true"></span></label>
                        @endif
                    @endforeach
                </div>
                <div class="cookie-modal-actions"><button type="button" class="button button-accent" data-cookie-action="save">Save preferences</button><button type="button" class="button button-light" data-cookie-action="accept">{{ $cookieSettings['accept_all_label'] }}</button><button type="button" class="cookie-text-button" data-cookie-action="reject">{{ $cookieSettings['reject_optional_label'] }}</button></div>
            </section>
        </div>
    </div>
@endif
