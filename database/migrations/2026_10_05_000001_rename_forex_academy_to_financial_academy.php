<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('business_units')->where('slug', 'forex')->update(['name' => 'Financial Academy']);
        DB::table('navigation_items')->where('label', 'Forex Academy')->update(['label' => 'Financial Academy']);
    }

    public function down(): void
    {
        DB::table('business_units')->where('slug', 'forex')->where('name', 'Financial Academy')->update(['name' => 'Forex Academy']);
        DB::table('navigation_items')->where('label', 'Financial Academy')->update(['label' => 'Forex Academy']);
    }
};
