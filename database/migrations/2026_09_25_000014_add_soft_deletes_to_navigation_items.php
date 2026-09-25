<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('navigation_items', 'deleted_at')) {
            Schema::table('navigation_items', fn (Blueprint $table) => $table->softDeletes());
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('navigation_items', 'deleted_at')) {
            Schema::table('navigation_items', fn (Blueprint $table) => $table->dropSoftDeletes());
        }
    }
};
