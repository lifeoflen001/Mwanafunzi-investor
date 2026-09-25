<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('key', 'home')->value('id');
        if (! $pageId) {
            return;
        }

        $now = now();
        $sections = [
            ['key' => 'philosophy', 'section_type' => 'split_content', 'heading' => 'Trading is not prediction. It is decision-making under uncertainty.', 'body' => 'The market does not owe us certainty. Mwanafunzi Investor exists to help you build a process that can hold up when certainty is impossible — through probability, rules and responsible risk.', 'cta_label' => 'Discover the framework', 'cta_url' => '#framework', 'payload' => ['eyebrow' => 'A different kind of market education'], 'sort_order' => 10],
            ['key' => 'learning', 'section_type' => 'featured_topics', 'heading' => 'Build the habits behind the chart.', 'body' => 'Four practical entry points for replacing noise with a calm, structured way of thinking about markets.', 'payload' => ['eyebrow' => 'Start with a stronger foundation'], 'sort_order' => 20],
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
        if ($pageId) {
            DB::table('page_sections')->where('page_id', $pageId)->delete();
        }
    }
};
