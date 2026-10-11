<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insertOrIgnore([
            ['key' => 'privacy.cookie.external_media_enabled', 'value' => '0', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'privacy.cookie.external_media_description', 'value' => 'Allows content hosted by external platforms to load directly on this site.', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'privacy.cookie.external_media_enabled',
            'privacy.cookie.external_media_description',
        ])->delete();
    }
};
