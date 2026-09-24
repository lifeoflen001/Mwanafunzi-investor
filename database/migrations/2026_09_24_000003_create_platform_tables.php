<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();
        });

        Schema::create('learning_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('short_description');
            $table->longText('full_description')->nullable();
            $table->string('image')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->string('skill_level')->nullable();
            $table->string('study_time')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('level')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('coming_soon');
            $table->boolean('is_featured')->default(false);
            $table->text('short_description');
            $table->longText('full_description')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('instructor')->nullable();
            $table->longText('cta_label')->nullable();
            $table->boolean('enrollment_available')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'is_featured']);
        });

        Schema::create('course_learning_topic', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_topic_id')->constrained()->cascadeOnDelete();
            $table->primary(['course_id', 'learning_topic_id']);
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->unique(['course_module_id', 'slug']);
        });

        Schema::create('course_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('product_type')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('short_description');
            $table->longText('detailed_description')->nullable();
            $table->string('version')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('availability')->default('coming_soon');
            $table->string('download_format')->nullable();
            $table->text('system_requirements')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('purchase_url')->nullable();
            $table->string('documentation_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['availability', 'is_featured']);
        });

        Schema::create('product_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->date('released_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('reading_time')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'published_at']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('article_tag', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'tag_id']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->morphs('faqable');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('category');
            $table->longText('message');
            $table->timestamp('consented_at')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('media');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('product_versions');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_features');
        Schema::dropIfExists('products');
        Schema::dropIfExists('course_faqs');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_learning_topic');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('learning_topics');
        Schema::dropIfExists('site_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
