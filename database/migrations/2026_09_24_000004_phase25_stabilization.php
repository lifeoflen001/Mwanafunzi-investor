<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'expected_availability')) $table->string('expected_availability')->nullable()->after('duration');
        });
        Schema::table('learning_topics', function (Blueprint $table) {
            if (! Schema::hasColumn('learning_topics', 'status')) $table->string('status')->default('published')->after('is_published');
            if (! Schema::hasColumn('learning_topics', 'expected_availability')) $table->string('expected_availability')->nullable()->after('study_time');
        });
        Schema::table('media', function (Blueprint $table) {
            if (! Schema::hasColumn('media', 'disk')) $table->string('disk')->default('public')->after('id');
            if (! Schema::hasColumn('media', 'variants')) $table->json('variants')->nullable()->after('title');
        });
        Schema::table('contact_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_messages', 'read_at')) $table->timestamp('read_at')->nullable()->after('status');
        });

        DB::table('learning_topics')->where('is_published', true)->update(['status' => 'published']);
        DB::table('learning_topics')->where('is_published', false)->update(['status' => 'draft']);
    }

    public function down(): void
    {
        Schema::table('courses', fn (Blueprint $table) => $table->dropColumn('expected_availability'));
        Schema::table('contact_messages', fn (Blueprint $table) => $table->dropColumn('read_at'));
        Schema::table('media', function (Blueprint $table) { $table->dropColumn(['disk', 'variants']); });
        Schema::table('learning_topics', function (Blueprint $table) { $table->dropColumn(['status', 'expected_availability']); });
    }
};
