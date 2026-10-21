<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the known lorem-style fixture from public presentation without deleting its CMS record.
        DB::table('testimonials')
            ->where('testimonial', 'Provident quo enim')
            ->update(['is_published' => false, 'is_featured' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Deliberately do not republish placeholder content when rolling migrations back.
    }
};
