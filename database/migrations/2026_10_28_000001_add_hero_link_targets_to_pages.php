<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('hero_primary_target', 20)->default('_self')->after('hero_primary_url');
            $table->string('hero_secondary_target', 20)->default('_self')->after('hero_secondary_url');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn(['hero_primary_target', 'hero_secondary_target']);
        });
    }
};
