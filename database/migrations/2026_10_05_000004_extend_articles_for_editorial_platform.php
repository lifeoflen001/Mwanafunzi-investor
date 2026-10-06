<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('content_type')->default('article')->after('content');
            $table->unsignedInteger('feature_priority')->default(0)->after('is_featured');
            $table->timestamp('feature_start_at')->nullable()->after('feature_priority');
            $table->timestamp('feature_end_at')->nullable()->after('feature_start_at');
            $table->foreignId('team_member_id')->nullable()->after('user_id')->constrained('team_members')->nullOnDelete();
            $table->index(['content_type', 'status', 'published_at']);
            $table->index(['is_featured', 'feature_priority', 'published_at']);
        });

        Schema::create('article_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 40);
            $table->string('url', 500);
            $table->string('label')->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['article_id', 'sort_order']);
        });

        Schema::create('article_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30)->default('like');
            $table->timestamps();
            $table->unique(['article_id', 'user_id', 'type']);
            $table->index(['article_id', 'type']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('body');
            $table->string('status', 20)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['article_id', 'status', 'created_at']);
            $table->index(['parent_id', 'status']);
        });

        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 40);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            $table->unique(['comment_id', 'user_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('article_reactions');
        Schema::dropIfExists('article_social_links');

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['team_member_id']);
            $table->dropIndex(['content_type', 'status', 'published_at']);
            $table->dropIndex(['is_featured', 'feature_priority', 'published_at']);
            $table->dropColumn(['content_type', 'feature_priority', 'feature_start_at', 'feature_end_at', 'team_member_id']);
        });
    }
};
