<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->boolean('is_active')->default(true)->after('seo_description');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn(['description', 'seo_title', 'seo_description', 'is_active', 'sort_order']);
        });
    }
};
