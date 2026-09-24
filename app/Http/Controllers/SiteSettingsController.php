<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Media;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    public function edit() { return view('admin.settings.edit', ['settings' => SiteSetting::orderBy('key')->get()->keyBy('key'), 'media' => Media::orderBy('filename')->get()]); }

    public function update(Request $request)
    {
        $data = $request->validate(['brand_name' => ['required', 'string', 'max:190'], 'contact_email' => ['required', 'email'], 'contact_phone' => ['nullable', 'string', 'max:40'], 'contact_whatsapp' => ['nullable', 'string', 'max:40'], 'contact_location' => ['nullable', 'string', 'max:120'], 'logo' => ['nullable', 'string', 'max:255'], 'dark_logo' => ['nullable', 'string', 'max:255'], 'favicon' => ['nullable', 'string', 'max:255'], 'default_social_image' => ['nullable', 'string', 'max:255'], 'footer_copy' => ['nullable', 'string', 'max:500'], 'default_seo_title' => ['required', 'string', 'max:190'], 'default_seo_description' => ['required', 'string', 'max:300'], 'risk_disclaimer' => ['required', 'string', 'max:600']]);
        foreach ($data as $key => $value) SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        return back()->with('success', 'Site settings updated.');
    }
}
