<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('media_type', 32)->nullable()->after('project_type');
            $table->string('creative_category', 120)->nullable()->after('media_type');
            $table->string('photography_type', 190)->nullable()->after('creative_category');
            $table->json('services_provided')->nullable()->after('photography_type');
            $table->string('location', 190)->nullable()->after('services_provided');
            $table->date('event_date')->nullable()->after('location');
            $table->string('featured_focal_point', 40)->nullable()->after('featured_image');
        });

        Schema::table('studio_inquiries', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('business_unit_id')->constrained('projects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('studio_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'media_type', 'creative_category', 'photography_type', 'services_provided',
                'location', 'event_date', 'featured_focal_point',
            ]);
        });
    }
};
