<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('course_lessons', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('course_modules', fn (Blueprint $table) => $table->dropSoftDeletes());
    }
};
