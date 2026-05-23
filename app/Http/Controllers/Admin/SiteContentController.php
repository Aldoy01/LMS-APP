<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function edit()
    {
        return view('admin.site-content.edit', [
            'settings' => SiteSetting::publicSettings(),
        ]);
    }

    public function update(Request $request)
    {
        $keys = array_keys(SiteSetting::DEFAULTS);

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.site_name' => ['required', 'string', 'max:120'],
            'settings.logo_url' => ['nullable', 'string', 'max:500'],
            'settings.hero_image_url' => ['nullable', 'string', 'max:500'],
            'settings.nav_home_label' => ['required', 'string', 'max:40'],
            'settings.nav_program_label' => ['required', 'string', 'max:40'],
            'settings.nav_contact_label' => ['required', 'string', 'max:40'],
            'settings.login_label' => ['required', 'string', 'max:40'],
            'settings.register_label' => ['required', 'string', 'max:40'],
            'settings.hero_title' => ['required', 'string', 'max:180'],
            'settings.hero_subtitle' => ['required', 'string', 'max:360'],
            'settings.hero_cta_label' => ['required', 'string', 'max:60'],
            'settings.hero_visual_badge' => ['required', 'string', 'max:80'],
            'settings.intro_eyebrow' => ['required', 'string', 'max:80'],
            'settings.intro_title' => ['required', 'string', 'max:120'],
            'settings.contact_whatsapp' => ['required', 'string', 'max:40'],
            'settings.contact_email' => ['required', 'email', 'max:120'],
            'settings.primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'settings.accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'settings.home_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        SiteSetting::saveManySettings(array_intersect_key(
            $data['settings'],
            array_flip($keys)
        ));

        return redirect()
            ->route('admin.site-content.edit')
            ->with('status', 'Konten dan tampilan halaman berhasil disimpan.');
    }
}
