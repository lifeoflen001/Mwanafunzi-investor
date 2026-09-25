<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pages', 'courses', 'products', 'articles', 'learning_topics'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->string('canonical_url')->nullable()->after($tableName === 'pages' ? 'seo_description' : 'og_image');
                $table->string('robots')->nullable()->after('canonical_url');
                $table->string('og_title')->nullable()->after('robots');
                $table->text('og_description')->nullable()->after('og_title');
            });
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->string('og_image')->nullable()->after('og_description');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'robots', 'og_title', 'og_description', 'og_image']);
        });

        foreach (['courses', 'products', 'articles', 'learning_topics'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['canonical_url', 'robots', 'og_title', 'og_description']);
            });
        }
    }
};
