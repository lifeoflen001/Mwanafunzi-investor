<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('admin_mfa_secret')->nullable();
            $table->boolean('admin_mfa_enabled')->default(false);
            $table->text('admin_mfa_recovery_codes')->nullable();
            $table->timestamp('admin_mfa_confirmed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_mfa_secret', 'admin_mfa_enabled', 'admin_mfa_recovery_codes', 'admin_mfa_confirmed_at']);
        });
    }
};
