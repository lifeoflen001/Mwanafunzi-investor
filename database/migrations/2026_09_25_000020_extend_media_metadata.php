<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->text('caption')->nullable()->after('alt_text');
            $table->foreignId('uploaded_by')->nullable()->after('caption')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['caption', 'uploaded_by']);
        });
    }
};
