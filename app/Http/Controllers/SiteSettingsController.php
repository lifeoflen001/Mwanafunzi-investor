<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Media;
use App\Support\DesignTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SiteSettingsController extends Controller
{
    public function edit() { return view('admin.settings.edit', ['settings' => SiteSetting::orderBy('key')->get()->keyBy('key'), 'media' => Media::orderBy('filename')->get(), 'designTokenDefinitions' => DesignTokens::definitions(), 'designTokenValues' => DesignTokens::values()]); }

    public function update(Request $request)
    {
        $data = $request->validate(array_merge(['brand_name' => ['required', 'string', 'max:190'], 'contact_email' => ['required', 'email'], 'contact_phone' => ['nullable', 'string', 'max:40'], 'contact_whatsapp' => ['nullable', 'string', 'max:40'], 'contact_location' => ['nullable', 'string', 'max:120'], 'logo' => ['nullable', 'string', 'max:255'], 'dark_logo' => ['nullable', 'string', 'max:255'], 'favicon' => ['nullable', 'string', 'max:255'], 'default_social_image' => ['nullable', 'string', 'max:255'], 'default_public_hero' => ['nullable', 'string', 'max:255'], 'hero_home_image' => ['nullable', 'string', 'max:255'], 'hero_learn_image' => ['nullable', 'string', 'max:255'], 'hero_courses_image' => ['nullable', 'string', 'max:255'], 'hero_tools_image' => ['nullable', 'string', 'max:255'], 'hero_journal_image' => ['nullable', 'string', 'max:255'], 'hero_about_image' => ['nullable', 'string', 'max:255'], 'hero_contact_image' => ['nullable', 'string', 'max:255'], 'hero_student_image' => ['nullable', 'string', 'max:255'], 'hero_legal_image' => ['nullable', 'string', 'max:255'], 'footer_copy' => ['nullable', 'string', 'max:500'], 'default_seo_title' => ['required', 'string', 'max:190'], 'default_seo_description' => ['required', 'string', 'max:300'], 'risk_disclaimer' => ['required', 'string', 'max:600']], DesignTokens::rules()));
        foreach (Arr::except($data, array_keys(DesignTokens::rules())) as $key => $value) SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        DesignTokens::save($data);
        return back()->with('success', 'Site settings updated.');
    }
}
