<?php

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::query()->firstOrCreate(
            ['key' => 'projects'],
            [
                'slug' => 'projects',
                'name' => 'Projects',
                'page_type' => 'index',
                'status' => 'published',
                'is_visible' => true,
                'hero_eyebrow' => 'Mwanafunzi Investor / Selected work',
                'hero_title' => 'Selected work',
                'hero_summary' => 'A considered selection of systems and digital products designed and built around real business needs.',
                'hero_overlay' => 'strong',
                'hero_alignment' => 'left',
                'seo_title' => 'Selected projects — Mwanafunzi Investor',
                'seo_description' => 'Selected systems, websites and digital products designed and built around real workflows.',
                'published_at' => now(),
            ]
        );

        foreach ([
            ['key' => 'case-studies', 'section_type' => 'feature_grid', 'heading' => 'Work with a point of view.', 'body' => 'Each project is shown with the context available: what needed to change, what was built and what the work made possible.', 'payload' => ['eyebrow' => 'Case studies'], 'sort_order' => 10],
            ['key' => 'empty-state', 'section_type' => 'empty_state', 'heading' => 'Projects are being prepared.', 'body' => 'Case studies will appear here as the work is documented and published.', 'payload' => ['eyebrow' => 'Selected work'], 'sort_order' => 20],
        ] as $data) {
            PageSection::query()->firstOrCreate(
                ['page_id' => $page->id, 'key' => $data['key']],
                $data + ['is_enabled' => true]
            );
        }
    }

    public function down(): void
    {
        // Additive CMS backfill is intentionally non-destructive.
    }
};
