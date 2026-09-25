<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_learning_topic', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_topic_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'learning_topic_id']);
        });

        Schema::create('learning_topic_product', function (Blueprint $table) {
            $table->foreignId('learning_topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['learning_topic_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_topic_product');
        Schema::dropIfExists('article_learning_topic');
    }
};
