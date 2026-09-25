<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pages' => 'hero_image', 'courses' => 'featured_image', 'products' => 'thumbnail', 'articles' => 'featured_image', 'learning_topics' => 'hero_image'] as $tableName => $after) {
            if (! Schema::hasColumn($tableName, 'hero_focal_point')) {
                Schema::table($tableName, function (Blueprint $table) use ($after) {
                    $table->string('hero_focal_point')->default('center center')->after($after);
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['pages', 'courses', 'products', 'articles', 'learning_topics'] as $tableName) {
            if (Schema::hasColumn($tableName, 'hero_focal_point')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('hero_focal_point');
                });
            }
        }
    }
};
