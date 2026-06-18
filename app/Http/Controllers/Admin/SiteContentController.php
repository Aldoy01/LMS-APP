<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

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
            'settings.participant_dashboard_image_url' => ['nullable', 'string', 'max:500'],
            'participant_dashboard_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'settings.nav_home_label' => ['required', 'string', 'max:40'],
            'settings.nav_program_label' => ['required', 'string', 'max:40'],
            'settings.nav_contact_label' => ['required', 'string', 'max:40'],
            'settings.login_label' => ['required', 'string', 'max:40'],
            'settings.register_label' => ['required', 'string', 'max:40'],
            'settings.hero_title' => ['required', 'string', 'max:180'],
            'settings.hero_subtitle' => ['required', 'string', 'max:360'],
            'settings.hero_slides' => ['nullable', 'string', 'max:3000'],
            'settings.hero_slide_images' => ['nullable', 'string', 'max:3000'],
            'hero_slide_images' => ['nullable', 'array', 'max:10'],
            'hero_slide_images.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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

        if ($request->hasFile('hero_slide_images')) {
            try {
                $directory = storage_path('app/public/site');

                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $currentImages = preg_split(
                    '/\r\n|\r|\n/',
                    $data['settings']['hero_slide_images'] ?? ''
                );
                $slideCount = collect(preg_split(
                    '/\r\n|\r|\n/',
                    $data['settings']['hero_slides'] ?? ''
                ))->filter(fn ($line) => trim($line) !== '')->count();

                $currentImages = array_pad($currentImages, max($slideCount, 1), '');

                foreach ($request->file('hero_slide_images') as $index => $file) {
                    if (! $file) {
                        continue;
                    }

                    $filename = 'hero-slide-' . ($index + 1) . '-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $file->move($directory, $filename);
                    $currentImages[$index] = 'site-media/' . $filename;
                }

                $data['settings']['hero_slide_images'] = implode(
                    "\n",
                    array_slice($currentImages, 0, max($slideCount, 1))
                );
            } catch (Throwable $exception) {
                return back()
                    ->withInput()
                    ->withErrors(['hero_slide_images' => 'Gambar slider gagal diupload. Gunakan JPG, PNG, atau WEBP maksimal 4MB per gambar.']);
            }
        }

        if ($request->hasFile('participant_dashboard_image')) {
            try {
                $file = $request->file('participant_dashboard_image');
                $directory = storage_path('app/public/site');

                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filename = 'participant-dashboard-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);

                $data['settings']['participant_dashboard_image_url'] = 'site-media/' . $filename;
            } catch (Throwable $exception) {
                return back()
                    ->withInput()
                    ->withErrors(['participant_dashboard_image' => 'Gambar gagal diupload. Coba gunakan file JPG/PNG/WEBP maksimal 4MB.']);
            }
        }

        SiteSetting::saveManySettings(array_intersect_key(
            $data['settings'],
            array_flip($keys)
        ));

        return redirect()
            ->route('admin.site-content.edit')
            ->with('status', 'Konten dan tampilan halaman berhasil disimpan.');
    }
}
