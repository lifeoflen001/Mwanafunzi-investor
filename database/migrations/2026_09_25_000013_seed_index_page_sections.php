<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $sections = [
            'learn' => ['heading' => 'Learn to build a process that can hold up under uncertainty.', 'body' => 'Four practical entry points for replacing noise with a calm, structured way of thinking about markets.', 'payload' => ['eyebrow' => 'Start with a stronger foundation']],
            'courses' => ['heading' => 'Courses for the long game.', 'body' => 'Practical education for people who are ready to build a process — without profitability guarantees or shortcuts.', 'payload' => ['eyebrow' => 'Learn in the right order']],
            'tools' => ['heading' => 'Make the process visible.', 'body' => 'Simple, focused tools for turning a good intention into a record you can learn from.', 'payload' => ['eyebrow' => 'Tools for deliberate practice']],
            'journal' => ['heading' => 'Real updates. Real journey.', 'body' => 'A living record of reviews, lessons and ideas worth returning to. No invented headlines. No made-up results.', 'payload' => ['eyebrow' => 'Field notes, when there are field notes']],
            'contact' => ['heading' => 'Start a conversation.', 'body' => 'Tell us what you are working on, what you want to learn or which Mwanafunzi Investor module you want to reach.', 'payload' => ['eyebrow' => 'The desk is open']],
            'student-of-money' => ['heading' => 'Good decisions become easier when they have somewhere to live.', 'body' => 'A Student of Money does not need to be right on every trade. They need controlled losses, repeatable execution and an edge that can express itself across a meaningful sample.', 'payload' => ['eyebrow' => 'The Student of Money']],
        ];

        foreach ($sections as $pageKey => $section) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if (! $pageId) continue;
            DB::table('page_sections')->updateOrInsert(
                ['page_id' => $pageId, 'key' => 'intro'],
                [
                    'section_type' => 'intro',
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                    'payload' => json_encode($section['payload'], JSON_UNESCAPED_UNICODE),
                    'sort_order' => 10,
                    'is_enabled' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        foreach (['learn', 'courses', 'tools', 'journal', 'contact', 'student-of-money'] as $pageKey) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if ($pageId) DB::table('page_sections')->where('page_id', $pageId)->where('key', 'intro')->delete();
        }
    }
};
