<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pages', 'courses', 'products', 'articles', 'learning_topics', 'services', 'projects', 'team_members'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'primary_keyword')) {
                    $table->string('primary_keyword')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'secondary_keywords')) {
                    $table->text('secondary_keywords')->nullable();
                }
            });
        }

        foreach (['services', 'team_members'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'og_title')) {
                    $table->string('og_title')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_description')) {
                    $table->text('og_description')->nullable();
                }
            });
        }

        foreach (['article_categories', 'tags'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'primary_keyword')) {
                    $table->string('primary_keyword')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'secondary_keywords')) {
                    $table->text('secondary_keywords')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'canonical_url')) {
                    $table->string('canonical_url')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'robots')) {
                    $table->string('robots')->default('index,follow');
                }
                if (! Schema::hasColumn($tableName, 'og_title')) {
                    $table->string('og_title')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_description')) {
                    $table->text('og_description')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_image')) {
                    $table->string('og_image')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['article_categories', 'tags'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = collect(['primary_keyword', 'secondary_keywords', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image'])
                    ->filter(fn (string $column) => Schema::hasColumn($tableName, $column))
                    ->values()
                    ->all();
                if ($columns) $table->dropColumn($columns);
            });
        }

        foreach (['pages', 'courses', 'products', 'articles', 'learning_topics', 'services', 'projects', 'team_members'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = collect(['primary_keyword', 'secondary_keywords', 'og_title', 'og_description'])
                    ->filter(fn (string $column) => Schema::hasColumn($tableName, $column))
                    ->values()
                    ->all();
                if ($columns) $table->dropColumn($columns);
            });
        }
    }
};
