<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteSetting extends Model
{
    public const DEFAULTS = [
        'site_name' => 'Trama Verse',
        'logo_url' => '',
        'hero_image_url' => '',
        'nav_home_label' => 'Home',
        'nav_program_label' => 'Program',
        'nav_contact_label' => 'Contact',
        'login_label' => 'Login',
        'register_label' => 'Register',
        'hero_title' => 'Bangun Karirmu sebagai Cyber Security Profesional',
        'hero_subtitle' => 'Pelajari Konsep dan Teknik Cyber Security dari para Pengajar Terbaik yang berpengalaman di Industri sampai Bisa!',
        'hero_cta_label' => 'Belajar Sekarang',
        'hero_visual_badge' => 'SQL  XSS  LAB',
        'intro_eyebrow' => 'Kenapa Trama Verse',
        'intro_title' => 'Cyber Learning yang Terarah',
        'contact_whatsapp' => '08513332305',
        'contact_email' => 'admin@tramaverse.test',
        'primary_color' => '#3157dc',
        'accent_color' => '#00d4ff',
        'home_background' => '#f8fbff',
    ];

    protected $fillable = ['key', 'value'];

    public static function publicSettings(): array
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return self::DEFAULTS;
            }

            return Cache::remember('site_settings.public', 300, function () {
                return array_replace(
                    self::DEFAULTS,
                    self::query()->pluck('value', 'key')->all()
                );
            });
        } catch (Throwable $exception) {
            return self::DEFAULTS;
        }
    }

    public static function saveManySettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        Cache::forget('site_settings.public');
    }
}
