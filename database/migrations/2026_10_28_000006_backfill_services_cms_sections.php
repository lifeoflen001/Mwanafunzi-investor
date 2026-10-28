<?php

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Keep the existing services page visible when a fresh database is built,
     * while making its already-public structure available to the page editor.
     */
    public function up(): void
    {
        $page = Page::query()->firstOrCreate(
            ['key' => 'services'],
            [
                'slug' => 'services',
                'name' => 'Services',
                'page_type' => 'index',
                'status' => 'published',
                'is_visible' => true,
                'hero_eyebrow' => 'Mwanafunzi Investor / Services',
                'hero_title' => 'Services built around real business needs',
                'hero_summary' => 'Digital software, websites and operational tools shaped around the way your business actually works.',
                'hero_overlay' => 'strong',
                'hero_alignment' => 'left',
                'seo_title' => 'Services — Mwanafunzi Investor',
                'seo_description' => 'Digital software, websites and operational tools shaped around real business needs.',
                'published_at' => now(),
            ]
        );

        $sections = [
            ['key' => 'divisions', 'section_type' => 'split_content', 'heading' => 'Choose the part of the work you need.', 'body' => 'Three connected divisions, each with its own route into the work.', 'payload' => ['eyebrow' => 'The Mwanafunzi ecosystem'], 'sort_order' => 10],
            ['key' => 'offer', 'section_type' => 'split_content', 'heading' => 'Practical systems, carefully built.', 'body' => 'Choose the kind of support that matches the problem in front of you. Pricing and delivery guidance is shown only when it has been configured.', 'payload' => ['eyebrow' => 'What I offer'], 'sort_order' => 20],
            ['key' => 'projects', 'section_type' => 'feature_grid', 'heading' => 'Systems built for the work behind the work.', 'body' => null, 'payload' => ['eyebrow' => 'Selected work'], 'sort_order' => 30],
            ['key' => 'final-cta', 'section_type' => 'cta', 'heading' => 'Tell me what you are trying to build.', 'body' => 'A short conversation is the best place to understand the problem, the people involved and what a useful outcome looks like.', 'payload' => ['eyebrow' => 'Have something specific in mind?'], 'sort_order' => 40],
        ];

        foreach ($sections as $data) {
            $section = PageSection::query()->firstOrCreate(
                ['page_id' => $page->id, 'key' => $data['key']],
                $data + ['is_enabled' => true]
            );

            $updates = [];
            if ($data['key'] === 'projects' && ! $section->cta_label) {
                $updates['cta_label'] = 'View all projects';
                $updates['cta_url'] = '/projects';
            }
            if ($data['key'] === 'final-cta' && ! $section->cta_label) {
                $updates['cta_label'] = 'Discuss your project';
                $updates['cta_url'] = '/contact?module=development';
            }
            if ($updates) {
                $section->update($updates);
            }
        }
    }

    /**
     * Do not delete administrator content when rolling migrations back.
     */
    public function down(): void
    {
        // Additive CMS backfill is intentionally non-destructive.
    }
};
