<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // These compound indexes match the published/list queries measured in
        // the public journal, CMS and portal flows; they are not blanket indexes.
        Schema::table('articles', fn (Blueprint $table) => $table->index(
            ['status', 'is_featured', 'published_at'],
            'articles_publish_feature_index'
        ));
        Schema::table('team_members', fn (Blueprint $table) => $table->index(
            ['is_active', 'sort_order', 'name'],
            'team_members_active_sort_index'
        ));
        Schema::table('page_sections', fn (Blueprint $table) => $table->index(
            ['page_id', 'is_enabled', 'sort_order'],
            'page_sections_page_enabled_sort_index'
        ));
        Schema::table('navigation_items', fn (Blueprint $table) => $table->index(
            ['location', 'parent_id', 'is_visible', 'sort_order'],
            'navigation_location_parent_visible_sort_index'
        ));
        Schema::table('comments', fn (Blueprint $table) => $table->index(
            ['article_id', 'status', 'parent_id', 'created_at'],
            'comments_article_status_parent_created_index'
        ));
        Schema::table('article_reactions', fn (Blueprint $table) => $table->index(
            ['article_id', 'user_id', 'type'],
            'article_reactions_article_user_type_index'
        ));
    }

    public function down(): void
    {
        Schema::table('article_reactions', fn (Blueprint $table) => $table->dropIndex('article_reactions_article_user_type_index'));
        Schema::table('comments', fn (Blueprint $table) => $table->dropIndex('comments_article_status_parent_created_index'));
        Schema::table('navigation_items', fn (Blueprint $table) => $table->dropIndex('navigation_location_parent_visible_sort_index'));
        Schema::table('page_sections', fn (Blueprint $table) => $table->dropIndex('page_sections_page_enabled_sort_index'));
        Schema::table('team_members', fn (Blueprint $table) => $table->dropIndex('team_members_active_sort_index'));
        Schema::table('articles', fn (Blueprint $table) => $table->dropIndex('articles_publish_feature_index'));
    }
};
