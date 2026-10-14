<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        foreach (['google_site_verification', 'bing_site_verification'] as $key) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => null, 'type' => 'text', 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['google_site_verification', 'bing_site_verification'])->delete();
    }
};
