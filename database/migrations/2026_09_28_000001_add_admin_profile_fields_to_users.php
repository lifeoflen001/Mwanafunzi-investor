<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'job_title')) $table->string('job_title')->nullable()->after('phone');
            if (! Schema::hasColumn('users', 'department')) $table->string('department')->nullable()->after('job_title');
            if (! Schema::hasColumn('users', 'bio')) $table->text('bio')->nullable()->after('department');
            if (! Schema::hasColumn('users', 'avatar_path')) $table->string('avatar_path')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['avatar_path', 'bio', 'department', 'job_title'] as $column) {
                if (Schema::hasColumn('users', $column)) $table->dropColumn($column);
            }
        });
    }
};
