<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('slug')->nullable()->unique();
            $table->string('name');
            $table->string('page_type')->default('standard');
            $table->string('status')->default('published');
            $table->boolean('is_visible')->default(true);
            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_summary')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_overlay')->default('medium');
            $table->string('hero_alignment')->default('left');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['page_type', 'status', 'is_visible']);
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('key');
            $table->string('section_type');
            $table->string('heading')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['page_id', 'key']);
            $table->index(['page_id', 'is_enabled', 'sort_order']);
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('location')->default('header');
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->string('label');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->string('cta_style')->default('link');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->index(['location', 'is_visible', 'sort_order']);
        });

        $now = now();
        $pages = [
            ['key' => 'home', 'name' => 'Home', 'page_type' => 'home', 'status' => 'published', 'is_visible' => true, 'seo_title' => 'Mwanafunzi Investor — Become a Student of Money', 'seo_description' => 'Financial education for systematic trading, probability, risk management and disciplined portfolio thinking.', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'learn', 'slug' => 'learn', 'name' => 'Learn', 'page_type' => 'index', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'courses', 'slug' => 'courses', 'name' => 'Courses', 'page_type' => 'index', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'tools', 'slug' => 'tools', 'name' => 'Tools', 'page_type' => 'index', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'journal', 'slug' => 'journal', 'name' => 'Journal', 'page_type' => 'index', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about', 'slug' => 'about', 'name' => 'About', 'page_type' => 'standard', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact', 'slug' => 'contact', 'name' => 'Contact', 'page_type' => 'form', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'student-of-money', 'slug' => 'student-of-money', 'name' => 'Student of Money', 'page_type' => 'standard', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'privacy-policy', 'slug' => 'privacy-policy', 'name' => 'Privacy Policy', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'terms', 'slug' => 'terms', 'name' => 'Terms', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'risk-disclosure', 'slug' => 'risk-disclosure', 'name' => 'Risk Disclosure', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'refund-policy', 'slug' => 'refund-policy', 'name' => 'Refund Policy', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'disclaimer', 'slug' => 'disclaimer', 'name' => 'Disclaimer', 'page_type' => 'policy', 'status' => 'published', 'is_visible' => true, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('pages')->insertOrIgnore(array_map(fn (array $page) => array_merge(['slug' => null, 'seo_title' => null, 'seo_description' => null], $page), $pages));

        $navigation = [
            ['location' => 'header', 'label' => 'Learn', 'route_name' => 'learn', 'sort_order' => 1, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'header', 'label' => 'Courses', 'route_name' => 'courses', 'sort_order' => 2, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'header', 'label' => 'Tools', 'route_name' => 'tools', 'sort_order' => 3, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'header', 'label' => 'Journal', 'route_name' => 'journal', 'sort_order' => 4, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'header', 'label' => 'About', 'route_name' => 'about', 'sort_order' => 5, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now],
            ['location' => 'header', 'label' => 'Contact', 'route_name' => 'contact', 'sort_order' => 6, 'is_visible' => true, 'cta_style' => 'button', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('navigation_items')->insertOrIgnore(array_map(fn (array $item) => array_merge(['target' => '_self', 'cta_style' => 'link'], $item), $navigation));
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};
