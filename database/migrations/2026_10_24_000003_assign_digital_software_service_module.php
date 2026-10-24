<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $developmentId = DB::table('business_units')
            ->where('slug', 'development')
            ->value('id');

        if ($developmentId) {
            DB::table('services')
                ->where('slug', 'digital-software-platforms')
                ->update([
                    'business_unit_id' => $developmentId,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('services')
            ->where('slug', 'digital-software-platforms')
            ->update([
                'business_unit_id' => null,
                'updated_at' => now(),
            ]);
    }
};
