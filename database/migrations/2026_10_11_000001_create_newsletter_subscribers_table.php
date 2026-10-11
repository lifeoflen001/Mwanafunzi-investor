<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->unique();
            $table->string('status', 24)->default('pending');
            $table->string('source', 32)->default('footer');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_confirmation_sent_at')->nullable();
            $table->string('confirmation_token_hash', 64)->nullable()->unique();
            $table->string('unsubscribe_token_hash', 64)->nullable()->unique();
            $table->text('unsubscribe_token')->nullable();
            $table->string('consent_version', 30)->nullable();
            $table->ipAddress('consent_ip')->nullable();
            $table->string('consent_user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
