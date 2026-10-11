<?php

namespace App\Http\Controllers;

use App\Models\CookieRegistry;
use App\Support\AdminAudit;
use App\Support\CookieConsentSettings;
use Illuminate\Http\Request;

class AdminPrivacyController extends Controller
{
    public function settings()
    {
        return view('admin.privacy.settings', ['settings' => CookieConsentSettings::all()]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'banner_heading' => ['required', 'string', 'max:190'], 'banner_description' => ['required', 'string', 'max:1000'], 'accept_all_label' => ['required', 'string', 'max:80'], 'reject_optional_label' => ['required', 'string', 'max:80'], 'manage_preferences_label' => ['required', 'string', 'max:80'], 'modal_heading' => ['required', 'string', 'max:190'],
            'lifetime_months' => ['required', 'integer', 'min:1', 'max:24'], 'version' => ['required', 'string', 'max:30'], 'policy_url' => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)/i'], 'privacy_url' => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)/i'],
            'essential_description' => ['required', 'string', 'max:500'], 'preferences_description' => ['required', 'string', 'max:500'], 'analytics_description' => ['required', 'string', 'max:500'], 'marketing_description' => ['required', 'string', 'max:500'], 'external_media_description' => ['required', 'string', 'max:500'],
            'enabled' => ['nullable', 'boolean'], 'analytics_enabled' => ['nullable', 'boolean'], 'marketing_enabled' => ['nullable', 'boolean'], 'external_media_enabled' => ['nullable', 'boolean'],
        ]);
        CookieConsentSettings::save([...$data, 'enabled' => $request->boolean('enabled'), 'preferences_enabled' => true, 'analytics_enabled' => $request->boolean('analytics_enabled'), 'marketing_enabled' => $request->boolean('marketing_enabled'), 'external_media_enabled' => $request->boolean('external_media_enabled')]);
        AdminAudit::record('privacy.cookie_settings_updated', 'Updated cookie consent settings');
        return back()->with('success', 'Cookie consent settings updated.');
    }

    public function registry(Request $request)
    {
        return view('admin.privacy.registry', ['entries' => CookieRegistry::orderBy('sort_order')->orderBy('name')->paginate(25), 'categories' => CookieRegistry::CATEGORIES]);
    }

    public function storeRegistry(Request $request)
    {
        $entry = CookieRegistry::create($this->validated($request));
        AdminAudit::record('privacy.registry_created', 'Added cookie/service registry entry '.$entry->name, $entry);
        return back()->with('success', 'Cookie/service entry added.');
    }

    public function updateRegistry(Request $request, CookieRegistry $entry)
    {
        $entry->update($this->validated($request));
        AdminAudit::record('privacy.registry_updated', 'Updated cookie/service registry entry '.$entry->name, $entry);
        return back()->with('success', 'Cookie/service entry updated.');
    }

    public function destroyRegistry(CookieRegistry $entry)
    {
        $name = $entry->name;
        $entry->delete();
        AdminAudit::record('privacy.registry_removed', 'Removed cookie/service registry entry '.$name);
        return back()->with('success', 'Cookie/service entry removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', 'in:cookie,service'], 'name' => ['required', 'string', 'max:190'], 'provider' => ['required', 'string', 'max:190'], 'purpose' => ['required', 'string', 'max:1000'], 'category' => ['required', 'in:'.implode(',', CookieRegistry::CATEGORIES)], 'expiry' => ['nullable', 'string', 'max:120'], 'type' => ['nullable', 'string', 'max:80'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
