<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_registry', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('cookie');
            $table->string('name', 190);
            $table->string('provider', 190);
            $table->text('purpose');
            $table->string('category', 24)->default('essential');
            $table->string('expiry', 120)->nullable();
            $table->string('type', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_active', 'sort_order']);
            $table->unique(['kind', 'name']);
        });

        $now = now();
        DB::table('cookie_registry')->insertOrIgnore([
            ['kind' => 'cookie', 'name' => 'laravel_session', 'provider' => 'Mwanafunzi Investor', 'purpose' => 'Maintains session state for forms, authentication and platform workflows.', 'category' => 'essential', 'expiry' => 'Session', 'type' => 'HTTP cookie', 'is_active' => true, 'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['kind' => 'cookie', 'name' => 'XSRF-TOKEN', 'provider' => 'Mwanafunzi Investor', 'purpose' => 'Helps protect form submissions from cross-site request forgery.', 'category' => 'essential', 'expiry' => 'Session', 'type' => 'HTTP cookie', 'is_active' => true, 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['kind' => 'cookie', 'name' => 'mwanafunzi_cookie_consent', 'provider' => 'Mwanafunzi Investor', 'purpose' => 'Remembers the optional cookie categories selected by the visitor.', 'category' => 'essential', 'expiry' => 'Configured consent lifetime', 'type' => 'HTTP cookie', 'is_active' => true, 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_registry');
    }
};
