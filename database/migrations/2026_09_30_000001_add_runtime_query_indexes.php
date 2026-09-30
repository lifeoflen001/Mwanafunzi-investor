<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', fn (Blueprint $table) => $table->index('path', 'media_path_index'));
        Schema::table('social_links', fn (Blueprint $table) => $table->index(['is_visible', 'sort_order'], 'social_links_visible_sort_index'));
        Schema::table('business_units', fn (Blueprint $table) => $table->index(['is_active', 'sort_order'], 'business_units_active_sort_index'));
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index('read_at', 'contact_messages_read_at_index');
            $table->index('created_at', 'contact_messages_created_at_index');
        });
        Schema::table('admin_audit_logs', fn (Blueprint $table) => $table->index('created_at', 'admin_audit_logs_created_at_index'));
    }

    public function down(): void
    {
        Schema::table('admin_audit_logs', fn (Blueprint $table) => $table->dropIndex('admin_audit_logs_created_at_index'));
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex('contact_messages_read_at_index');
            $table->dropIndex('contact_messages_created_at_index');
        });
        Schema::table('business_units', fn (Blueprint $table) => $table->dropIndex('business_units_active_sort_index'));
        Schema::table('social_links', fn (Blueprint $table) => $table->dropIndex('social_links_visible_sort_index'));
        Schema::table('media', fn (Blueprint $table) => $table->dropIndex('media_path_index'));
    }
};
