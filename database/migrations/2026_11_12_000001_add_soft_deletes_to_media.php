<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('media', 'deleted_at')) {
            Schema::table('media', function (Blueprint $table): void {
                $table->softDeletes();
                $table->index('deleted_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('media', 'deleted_at')) {
            Schema::table('media', function (Blueprint $table): void {
                $table->dropIndex(['deleted_at']);
                $table->dropSoftDeletes();
            });
        }
    }
};
