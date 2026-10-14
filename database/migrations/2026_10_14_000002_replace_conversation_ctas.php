<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('hero_title', 'Start a conversation.')
            ->update(['hero_title' => 'Contact us.', 'updated_at' => now()]);

        DB::table('page_sections')
            ->where('cta_label', 'Start a conversation')
            ->update(['cta_label' => 'Contact us', 'updated_at' => now()]);

        DB::table('page_sections')
            ->where('heading', 'Start a conversation.')
            ->update(['heading' => 'Contact us.', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('pages')
            ->where('hero_title', 'Contact us.')
            ->update(['hero_title' => 'Start a conversation.', 'updated_at' => now()]);

        DB::table('page_sections')
            ->where('cta_label', 'Contact us')
            ->update(['cta_label' => 'Start a conversation', 'updated_at' => now()]);

        DB::table('page_sections')
            ->where('heading', 'Contact us.')
            ->update(['heading' => 'Start a conversation.', 'updated_at' => now()]);
    }
};
