<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('page_sections')
            ->where('page_id', DB::table('pages')->where('key', 'cookie-policy')->value('id'))
            ->where('key', 'optional-categories')
            ->update([
                'body' => 'Preferences, analytics, marketing and external-media categories are disabled until you choose them. Optional services are only loaded when the relevant category is enabled and the service is configured.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('page_sections')
            ->where('page_id', DB::table('pages')->where('key', 'cookie-policy')->value('id'))
            ->where('key', 'optional-categories')
            ->update([
                'body' => 'Preferences, analytics and marketing categories are disabled until you choose them. Optional services are only loaded when the relevant category is enabled and the service is configured.',
                'updated_at' => now(),
            ]);
    }
};
