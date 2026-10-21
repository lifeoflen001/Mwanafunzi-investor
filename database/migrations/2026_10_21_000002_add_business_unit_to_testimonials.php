<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()->after('project_id')->constrained('business_units')->nullOnDelete();
            $table->index(['business_unit_id', 'is_published', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex(['business_unit_id', 'is_published', 'is_featured']);
            $table->dropConstrainedForeignId('business_unit_id');
        });
    }
};
