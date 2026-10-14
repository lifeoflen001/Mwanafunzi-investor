<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')
            ->whereIn('key', ['design_night', 'design_portal_sidebar', 'design_admin_sidebar'])
            ->where('value', '#101c24')
            ->update(['value' => '#111211', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', ['design_night', 'design_portal_sidebar', 'design_admin_sidebar'])
            ->where('value', '#111211')
            ->update(['value' => '#101c24', 'updated_at' => now()]);
    }
};
