<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::firstOrCreate(
            ['key' => 'faqs'],
            [
                'slug' => 'faqs',
                'name' => 'FAQs',
                'page_type' => 'index',
                'status' => 'published',
                'is_visible' => true,
                'hero_eyebrow' => 'Questions, made clear',
                'hero_title' => 'Frequently asked questions.',
                'hero_summary' => 'A practical place to find answers about learning, tools and the work we do.',
                'seo_title' => 'Frequently asked questions — Mwanafunzi Investor',
                'seo_description' => 'Find clear answers about Mwanafunzi Investor courses, learning paths, digital tools and platform services.',
            ],
        );

        PageSection::firstOrCreate(
            ['page_id' => $page->id, 'key' => 'intro'],
            [
                'section_type' => 'split_content',
                'heading' => 'Start with the question you have.',
                'body' => 'These answers are connected to currently published platform content and are kept deliberately direct.',
                'payload' => ['eyebrow' => 'Answers'],
                'sort_order' => 10,
                'is_enabled' => true,
            ],
        );

        foreach ([
            'header_contact_label' => 'Contact us',
            'footer_brand_context' => 'Clear, practical lessons on markets, risk and probability.',
            'footer_company_label' => 'Company',
            'footer_explore_label' => 'Explore',
            'footer_legal_label' => 'Legal',
            'footer_contact_label' => 'Contact',
            'footer_social_label' => 'Follow',
            'footer_newsletter_eyebrow' => 'Stay connected',
        ] as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        }
    }

    public function down(): void
    {
        // The migration only backfills missing records and never removes
        // content that an administrator may have edited afterwards.
    }
};
