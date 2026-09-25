<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_topics', function (Blueprint $table) {
            $table->string('hero_eyebrow')->nullable()->after('image');
            $table->string('hero_title')->nullable()->after('hero_eyebrow');
            $table->text('hero_summary')->nullable()->after('hero_title');
            $table->string('hero_image')->nullable()->after('hero_summary');
            $table->string('hero_overlay')->default('medium')->after('hero_image');
            $table->string('hero_alignment')->default('left')->after('hero_overlay');
            $table->string('seo_title')->nullable()->after('hero_alignment');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('og_image')->nullable()->after('seo_description');
        });
    }

    public function down(): void
    {
        Schema::table('learning_topics', function (Blueprint $table) {
            $table->dropColumn(['hero_eyebrow', 'hero_title', 'hero_summary', 'hero_image', 'hero_overlay', 'hero_alignment', 'seo_title', 'seo_description', 'og_image']);
        });
    }
};
