<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('key', 'home')->value('id');
        if (! $pageId) return;

        $now = now();
        $sections = [
            ['key' => 'framework', 'section_type' => 'framework', 'heading' => 'The Student of Money Framework.', 'body' => 'Good decisions become easier when they have somewhere to live.', 'payload' => ['eyebrow' => 'A process you can come back to', 'steps' => [['title' => 'Learn', 'body' => 'Understand the market and your own assumptions.'], ['title' => 'Define Rules', 'body' => 'Write down what must be true before you act.'], ['title' => 'Plan Risk', 'body' => 'Decide what you can responsibly put at risk.'], ['title' => 'Execute', 'body' => 'Follow the plan without negotiating with fear.'], ['title' => 'Journal', 'body' => 'Capture the decision while the context is clear.'], ['title' => 'Review', 'body' => 'Look for patterns across a meaningful sample.'], ['title' => 'Improve', 'body' => 'Make one thoughtful change at a time.']]], 'sort_order' => 30],
            ['key' => 'tools', 'section_type' => 'featured_products', 'heading' => 'Make the process visible.', 'body' => 'Simple, focused tools for turning a good intention into a record you can learn from. Product availability and pricing can be configured as the catalogue grows.', 'payload' => ['eyebrow' => 'Tools for deliberate practice'], 'sort_order' => 40],
            ['key' => 'probability', 'section_type' => 'split_content', 'heading' => "You don't need to be right on every trade.", 'body' => 'You need controlled losses, repeatable execution and an edge that can express itself across a meaningful sample of trades.', 'payload' => ['eyebrow' => 'The mathematics of staying in the game'], 'sort_order' => 50],
            ['key' => 'discipline', 'section_type' => 'rich_text', 'heading' => 'Discipline Beyond The Charts.', 'body' => 'Patience. Stewardship. Self-control. Humility. Consistency. Responsibility.\n\nFinancial education is also an education in how we carry uncertainty. The point is not perfection — it is becoming trustworthy with the decisions in front of you.', 'payload' => ['eyebrow' => 'The work beyond the chart'], 'sort_order' => 60],
            ['key' => 'journal', 'section_type' => 'latest_journal', 'heading' => 'Real Updates. Real Journey.', 'body' => 'The journal will be a living record of the work: reviews, lessons and ideas worth returning to. No invented headlines. No made-up results.', 'payload' => ['eyebrow' => 'Field notes, when there are field notes'], 'sort_order' => 70],
            ['key' => 'courses', 'section_type' => 'featured_courses', 'heading' => 'Courses for the long game.', 'body' => 'Practical education for people who are ready to build a process — without profitability guarantees or shortcuts.', 'payload' => ['eyebrow' => 'Learn in the right order'], 'sort_order' => 80],
            ['key' => 'final_cta', 'section_type' => 'cta', 'heading' => "Markets will always be uncertain. Your process doesn't have to be.", 'body' => 'Become a Student of Money.', 'cta_label' => 'Start Learning', 'cta_url' => 'mailto:mwanafunziinvestor@outlook.com?subject=Start%20Learning', 'payload' => ['eyebrow' => 'Come back to the process'], 'sort_order' => 90],
        ];

        foreach ($sections as $section) {
            $section['payload'] = json_encode($section['payload'], JSON_UNESCAPED_UNICODE);
            DB::table('page_sections')->updateOrInsert(
                ['page_id' => $pageId, 'key' => $section['key']],
                array_merge($section, ['page_id' => $pageId, 'is_enabled' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('key', 'home')->value('id');
        if ($pageId) DB::table('page_sections')->where('page_id', $pageId)->whereIn('key', ['framework', 'tools', 'probability', 'discipline', 'journal', 'courses', 'final_cta'])->delete();
    }
};
