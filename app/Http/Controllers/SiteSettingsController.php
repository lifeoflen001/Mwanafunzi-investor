<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Media;
use App\Support\DesignTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Support\AdminAudit;
use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Crypt;

class SiteSettingsController extends Controller
{
    public function edit() { return view('admin.settings.edit', ['settings' => SiteSetting::orderBy('key')->get()->keyBy('key'), 'media' => Media::latest()->limit(60)->get(), 'designTokenDefinitions' => DesignTokens::definitions(), 'designTokenValues' => DesignTokens::values()]); }

    public function security(RecaptchaService $recaptcha)
    {
        $checks = [
            ['label' => 'Production debug mode', 'ok' => ! config('app.debug'), 'value' => config('app.debug') ? 'Enabled — disable before deployment' : 'Disabled'],
            ['label' => 'Admin MFA policy', 'ok' => (bool) config('security.admin_mfa_required'), 'value' => config('security.admin_mfa_required') ? 'Required' : 'Optional'],
            ['label' => 'Secure session cookies', 'ok' => (bool) config('session.secure') || app()->environment(['local', 'testing']), 'value' => config('session.secure') ? 'HTTPS only' : 'Local/deployment override'],
            ['label' => 'Private product storage', 'ok' => config('filesystems.disks.local.serve') === false, 'value' => config('filesystems.disks.local.serve') === false ? 'Not publicly served' : 'Review storage exposure'],
            ['label' => 'Security response headers', 'ok' => true, 'value' => 'Global middleware enabled'],
            ['label' => 'reCAPTCHA credentials', 'ok' => ! $recaptcha->isEnabled() || ($recaptcha->hasSecret() && filled($recaptcha->siteKey())), 'value' => ! $recaptcha->isEnabled() ? 'Disabled' : ($recaptcha->hasSecret() && filled($recaptcha->siteKey()) ? 'Configured' : 'Incomplete configuration')],
        ];

        return view('admin.settings.security', compact('recaptcha', 'checks'));
    }

    public function updateSecurity(Request $request, RecaptchaService $recaptcha)
    {
        $data = $request->validate([
            'recaptcha_enabled' => ['nullable', 'boolean'],
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_min_score' => ['required', 'numeric', 'min:0', 'max:1'],
            'recaptcha_hostname' => ['nullable', 'string', 'max:190'],
            'recaptcha_forms' => ['nullable', 'array'],
            'recaptcha_forms.*' => ['boolean'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'security.recaptcha.enabled'], ['value' => $request->boolean('recaptcha_enabled') ? '1' : '0', 'type' => 'boolean']);
        SiteSetting::updateOrCreate(['key' => 'security.recaptcha.site_key'], ['value' => (string) ($data['recaptcha_site_key'] ?? ''), 'type' => 'text']);
        SiteSetting::updateOrCreate(['key' => 'security.recaptcha.min_score'], ['value' => (string) $data['recaptcha_min_score'], 'type' => 'text']);
        SiteSetting::updateOrCreate(['key' => 'security.recaptcha.hostname'], ['value' => (string) ($data['recaptcha_hostname'] ?? ''), 'type' => 'text']);
        SiteSetting::updateOrCreate(['key' => 'security.recaptcha.forms'], ['value' => json_encode(collect($recaptcha->formSettings())->mapWithKeys(fn ($enabled, $form) => [$form => array_key_exists($form, $data['recaptcha_forms'] ?? []) ? (bool) $data['recaptcha_forms'][$form] : (bool) $enabled])->all()), 'type' => 'json']);

        if (filled($data['recaptcha_secret_key'] ?? null)) {
            SiteSetting::updateOrCreate(['key' => 'security.recaptcha.secret_key'], ['value' => Crypt::encryptString($data['recaptcha_secret_key']), 'type' => 'encrypted']);
        }

        SiteSetting::forgetCache();
        AdminAudit::record('security.settings_updated', 'Updated reCAPTCHA security settings');

        return back()->with('success', 'Security settings updated. Secret values remain masked after saving.');
    }

    public function update(Request $request)
    {
        $data = $request->validate(array_merge(['brand_name' => ['required', 'string', 'max:190'], 'contact_email' => ['required', 'email'], 'contact_phone' => ['nullable', 'string', 'max:40'], 'contact_whatsapp' => ['nullable', 'string', 'max:40'], 'contact_location' => ['nullable', 'string', 'max:120'], 'logo' => ['nullable', 'string', 'max:255'], 'dark_logo' => ['nullable', 'string', 'max:255'], 'light_logo' => ['nullable', 'string', 'max:255'], 'icon_logo' => ['nullable', 'string', 'max:255'], 'favicon' => ['nullable', 'string', 'max:255'], 'apple_touch_icon' => ['nullable', 'string', 'max:255'], 'default_social_image' => ['nullable', 'string', 'max:255'], 'default_public_hero' => ['nullable', 'string', 'max:255'], 'hero_home_image' => ['nullable', 'string', 'max:255'], 'hero_learn_image' => ['nullable', 'string', 'max:255'], 'hero_courses_image' => ['nullable', 'string', 'max:255'], 'hero_tools_image' => ['nullable', 'string', 'max:255'], 'hero_journal_image' => ['nullable', 'string', 'max:255'], 'hero_about_image' => ['nullable', 'string', 'max:255'], 'hero_contact_image' => ['nullable', 'string', 'max:255'], 'hero_student_image' => ['nullable', 'string', 'max:255'], 'hero_legal_image' => ['nullable', 'string', 'max:255'], 'footer_copy' => ['nullable', 'string', 'max:500'], 'footer_copyright' => ['nullable', 'string', 'max:190'], 'footer_bottom_statement' => ['nullable', 'string', 'max:190'], 'default_seo_title' => ['required', 'string', 'max:190'], 'default_seo_description' => ['required', 'string', 'max:300'], 'risk_disclaimer' => ['required', 'string', 'max:600']], DesignTokens::rules()));
        foreach (Arr::except($data, array_keys(DesignTokens::rules())) as $key => $value) SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        DesignTokens::save($data);
        SiteSetting::forgetCache();
        AdminAudit::record('settings.updated', 'Updated site settings');
        return back()->with('success', 'Site settings updated.');
    }
}
