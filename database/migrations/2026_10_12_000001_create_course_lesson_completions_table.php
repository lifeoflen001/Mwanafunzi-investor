<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['user_id', 'enrollment_id', 'course_lesson_id'], 'lesson_completion_identity');
            $table->index(['enrollment_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lesson_completions');
    }
};
