<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('detailed_description')->nullable();
            $table->string('icon')->nullable();
            $table->string('featured_image')->nullable();
            $table->decimal('starting_price', 14, 2)->nullable();
            $table->string('pricing_label')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('pricing_type')->default('custom_quote');
            $table->string('delivery_estimate')->nullable();
            $table->json('included_features')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'is_featured', 'sort_order']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('project_type')->nullable();
            $table->text('short_description');
            $table->longText('full_introduction')->nullable();
            $table->longText('challenge')->nullable();
            $table->longText('role')->nullable();
            $table->longText('approach')->nullable();
            $table->longText('solution')->nullable();
            $table->string('timeline')->nullable();
            $table->string('platform')->nullable();
            $table->string('status')->default('draft');
            $table->date('project_date')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('technologies')->nullable();
            $table->json('features')->nullable();
            $table->json('results')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'is_featured', 'display_order']);
        });

        Schema::create('project_service', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'service_id']);
        });

        Schema::create('project_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['project_id', 'media_id']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_role')->nullable();
            $table->string('company')->nullable();
            $table->longText('testimonial');
            $table->string('client_photo')->nullable();
            $table->string('company_logo')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('anonymous_display')->default(false);
            $table->boolean('initials_only')->default(false);
            $table->boolean('hide_company')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('testimonial_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('project_media');
        Schema::dropIfExists('project_service');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
    }
};
