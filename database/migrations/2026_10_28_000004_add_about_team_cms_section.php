<?php

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::where('key', 'about')->first();
        if (! $page) {
            return;
        }

        PageSection::firstOrCreate(
            ['page_id' => $page->id, 'key' => 'team'],
            [
                'section_type' => 'feature_grid',
                'heading' => 'A small team with a serious point of view.',
                'body' => 'Good work is built by people who care about the details, stay curious and keep the process honest.',
                'payload' => ['eyebrow' => 'The people behind the work'],
                'sort_order' => 40,
                'is_enabled' => true,
            ],
        );
    }

    public function down(): void
    {
        // Preserve any administrator-edited section if migrations are rolled back.
    }
};
