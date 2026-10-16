<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable()->change();
        });

        Schema::create('social_identities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 40);
            $table->string('provider_user_id', 190);
            $table->string('provider_email')->nullable();
            $table->text('avatar_url')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_user_id']);
            $table->unique(['user_id', 'provider']);
            $table->index(['provider', 'provider_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_identities');

        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable(false)->change();
        });
    }
};
