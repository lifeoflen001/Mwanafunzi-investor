<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'expected_availability')) {
            Schema::table('courses', fn (Blueprint $table) => $table->string('expected_availability')->nullable()->after('duration'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'expected_availability')) {
            Schema::table('courses', fn (Blueprint $table) => $table->dropColumn('expected_availability'));
        }
    }
};
