<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('source')->nullable()->after('caption');
            $table->string('copyright_owner')->nullable()->after('source');
            $table->string('license')->nullable()->after('copyright_owner');
            $table->string('license_url')->nullable()->after('license');
            $table->boolean('attribution_required')->default(false)->after('license_url');
            $table->string('attribution_text')->nullable()->after('attribution_required');
            $table->text('internal_usage_notes')->nullable()->after('attribution_text');
            $table->boolean('rights_verified')->default(false)->after('internal_usage_notes');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->boolean('consent_confirmed')->default(false)->after('source_url');
            $table->timestamp('consent_confirmed_at')->nullable()->after('consent_confirmed');
            $table->text('consent_notes')->nullable()->after('consent_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['consent_confirmed', 'consent_confirmed_at', 'consent_notes']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['source', 'copyright_owner', 'license', 'license_url', 'attribution_required', 'attribution_text', 'internal_usage_notes', 'rights_verified']);
        });
    }
};
