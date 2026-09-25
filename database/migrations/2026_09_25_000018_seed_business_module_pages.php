<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $pages = [
            'development' => [
                'slug' => 'development',
                'name' => 'Digital Systems',
                'hero_eyebrow' => 'Mwanafunzi Investor / Digital Systems',
                'hero_title' => 'Digital tools for work that needs to',
                'hero_highlight' => 'move.',
                'hero_summary' => 'We design and build clear, dependable websites and software for organisations that are ready to work with less friction.',
                'hero_primary_label' => 'Discuss a project',
                'hero_primary_url' => '/contact?module=development',
                'hero_secondary_label' => 'See what we build',
                'hero_secondary_url' => '#capabilities',
            ],
            'studio' => [
                'slug' => 'studio',
                'name' => 'Creative Studio',
                'hero_eyebrow' => 'Mwanafunzi Investor / Creative Studio',
                'hero_title' => 'Make the moment',
                'hero_highlight' => 'stay.',
                'hero_summary' => 'Photography and video for people, brands and organisations with something worth seeing, remembering and sharing.',
                'hero_primary_label' => 'Plan a shoot',
                'hero_primary_url' => '/contact?module=studio',
                'hero_secondary_label' => 'Explore the studio',
                'hero_secondary_url' => '#services',
            ],
        ];

        foreach ($pages as $key => $page) {
            $pageId = DB::table('pages')->where('key', $key)->value('id');
            $attributes = array_merge([
                'key' => $key,
                'page_type' => 'standard',
                'status' => 'published',
                'is_visible' => true,
                'hero_overlay' => 'strong',
                'hero_alignment' => 'left',
                'published_at' => $now,
                'seo_title' => $page['name'].' — Mwanafunzi Investor',
                'seo_description' => $page['hero_summary'],
                'created_at' => $now,
                'updated_at' => $now,
            ], $page);

            if ($pageId) {
                DB::table('pages')->where('id', $pageId)->update(array_merge($attributes, ['updated_at' => $now]));
            } else {
                $pageId = DB::table('pages')->insertGetId($attributes);
            }

            $sections = $key === 'development' ? $this->developmentSections() : $this->studioSections();
            foreach ($sections as $section) {
                DB::table('page_sections')->updateOrInsert(
                    ['page_id' => $pageId, 'key' => $section['key']],
                    array_merge($section, [
                        'page_id' => $pageId,
                        'payload' => isset($section['payload']) ? json_encode($section['payload'], JSON_UNESCAPED_UNICODE) : null,
                        'is_enabled' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }
    }

    private function developmentSections(): array
    {
        return [
            ['key' => 'capabilities', 'section_type' => 'feature_grid', 'heading' => 'Systems that make the next step clearer.', 'body' => 'Every project starts with the work behind the brief: what needs to happen, who needs to use it and where the current process gets in the way.', 'payload' => ['eyebrow' => 'What we build', 'cards' => [
                ['index' => '01', 'title' => 'Websites that earn attention', 'body' => 'Purposeful public-facing websites with strong structure, responsive layouts and content that helps people decide.', 'tags' => 'Strategy · Design · Build'],
                ['index' => '02', 'title' => 'Software that reduces friction', 'body' => 'Custom web applications, dashboards and internal tools that turn repeated work into a clearer system.', 'tags' => 'Laravel · Interfaces · Workflows'],
                ['index' => '03', 'title' => 'Commerce and integrations', 'body' => 'Practical commerce experiences and integrations that connect customers, payments, content and operations.', 'tags' => 'Commerce · Payments · APIs'],
                ['index' => '04', 'title' => 'Care after launch', 'body' => 'Measured improvements, maintenance and support so the system keeps earning its place as the work grows.', 'tags' => 'Support · Iteration · Growth'],
            ]], 'sort_order' => 10],
            ['key' => 'process', 'section_type' => 'framework', 'heading' => 'Good work is a process.', 'body' => 'Clear decisions early make better products later. We keep the work visible, collaborative and grounded in the actual problem.', 'payload' => ['eyebrow' => 'How we work', 'steps' => [
                ['title' => 'Understand', 'body' => 'Map the audience, goals, constraints and the work the system must support.'],
                ['title' => 'Shape', 'body' => 'Turn the brief into a focused structure, useful flows and a visual direction.'],
                ['title' => 'Build', 'body' => 'Develop in visible stages, test the important paths and keep the foundation maintainable.'],
                ['title' => 'Improve', 'body' => 'Launch with care, learn from use and make the next version more useful.'],
            ]], 'sort_order' => 20],
            ['key' => 'final_cta', 'section_type' => 'cta', 'heading' => 'Bring the problem.', 'body' => 'We’ll shape the system.', 'cta_label' => 'Start a conversation', 'cta_url' => '/contact?module=development', 'payload' => ['eyebrow' => 'Have a project in mind?'], 'sort_order' => 30],
        ];
    }

    private function studioSections(): array
    {
        return [
            ['key' => 'services', 'section_type' => 'feature_grid', 'heading' => 'Images with a reason to exist.', 'body' => 'The right frame is more than a record of what happened. It gives a person, product or place a clearer way to be understood.', 'payload' => ['eyebrow' => 'What we make', 'cards' => [
                ['index' => '01', 'title' => 'Brand and campaign', 'body' => 'Visual direction and image-making for brands that need a consistent, recognisable point of view.', 'tags' => ''],
                ['index' => '02', 'title' => 'Portraits and people', 'body' => 'Thoughtful portraits for founders, teams, creatives and people who want to be seen as themselves.', 'tags' => ''],
                ['index' => '03', 'title' => 'Events and documentary', 'body' => 'Observational coverage that preserves the energy, details and people that make an event matter.', 'tags' => ''],
                ['index' => '04', 'title' => 'Short-form video', 'body' => 'Focused social and campaign films built around a clear message, strong pacing and useful delivery formats.', 'tags' => ''],
                ['index' => '05', 'title' => 'Interviews and stories', 'body' => 'Human-led video for organisations that need to explain their work through real voices and real context.', 'tags' => ''],
                ['index' => '06', 'title' => 'Post-production', 'body' => 'Editing, colour, sound and final exports that help the finished work feel considered wherever it is used.', 'tags' => ''],
            ]], 'sort_order' => 10],
            ['key' => 'approach', 'section_type' => 'split_content', 'heading' => 'Calm on set. Care in the frame.', 'body' => 'Good visual work starts before the camera comes out. We take time to understand the people, mood and purpose behind the brief so the final images feel natural and useful.', 'payload' => ['eyebrow' => 'The approach', 'list' => ['Clear creative direction before production', 'A considered plan for people, place and light', 'Practical formats for web, social and print', 'Organised delivery of the final selected work']], 'sort_order' => 20],
            ['key' => 'process', 'section_type' => 'framework', 'heading' => 'Make room for the real.', 'body' => 'We keep production structured enough to feel dependable and open enough to let the honest moment happen.', 'payload' => ['eyebrow' => 'From brief to delivery', 'steps' => [
                ['title' => 'Brief', 'body' => 'We clarify the story, audience, mood and practical requirements.'],
                ['title' => 'Prepare', 'body' => 'We plan the location, schedule, people, shot list and production details.'],
                ['title' => 'Capture', 'body' => 'We create an environment where the useful, natural frame can appear.'],
                ['title' => 'Deliver', 'body' => 'We refine, organise and export the work for the places it needs to go.'],
            ]], 'sort_order' => 30],
            ['key' => 'final_cta', 'section_type' => 'cta', 'heading' => 'There is a story in the room.', 'body' => 'Share the idea, the occasion or the feeling you need to capture. We will help you shape the right kind of shoot.', 'cta_label' => 'Start a conversation', 'cta_url' => '/contact?module=studio', 'payload' => ['eyebrow' => 'Ready when you are'], 'sort_order' => 40],
        ];
    }

    public function down(): void
    {
        foreach (['development', 'studio'] as $key) {
            $pageId = DB::table('pages')->where('key', $key)->value('id');
            if ($pageId) {
                DB::table('page_sections')->where('page_id', $pageId)->delete();
                DB::table('pages')->where('id', $pageId)->delete();
            }
        }
    }
};
