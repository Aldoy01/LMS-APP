<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $now = now();

        foreach ([
            'site_name' => 'Trama Verse',
            'logo_url' => '',
            'intro_eyebrow' => 'Kenapa Trama Verse',
            'contact_email' => 'admin@tramaverse.test',
        ] as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        Cache::forget('site_settings.public');
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $now = now();

        foreach ([
            'site_name' => 'TECHVERSE Learning',
            'logo_url' => '',
            'intro_eyebrow' => 'Kenapa TECHVERSE Learning',
            'contact_email' => 'admin@techverselearning.test',
        ] as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        Cache::forget('site_settings.public');
    }
};
