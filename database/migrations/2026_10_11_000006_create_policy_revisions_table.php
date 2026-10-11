<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->unsignedInteger('revision')->default(1);
            $table->string('page_name');
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->date('effective_date')->nullable();
            $table->json('sections')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['page_id', 'revision']);
            $table->index(['page_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_revisions');
    }
};
