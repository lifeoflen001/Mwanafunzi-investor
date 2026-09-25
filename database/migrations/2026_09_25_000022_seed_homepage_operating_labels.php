<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'philosophy' => ['visual_note' => 'Always a', 'visual_note_emphasis' => 'mwanafunzi.'],
            'learning' => [
                'card_cta_label' => 'Learn more',
                'empty_index' => 'LEARN',
                'empty_heading' => 'The first learning paths are being prepared.',
                'empty_body' => 'Check back soon.',
            ],
            'tools' => [
                'card_cta_label' => 'View tool',
                'empty_index' => 'TOOLS',
                'empty_heading' => 'The first tools are being prepared.',
                'empty_body' => 'Product availability will appear here when ready.',
            ],
            'journal' => [
                'card_cta_label' => 'Read the note',
                'empty_index' => 'J / 00',
                'empty_heading' => 'The next entry is being studied.',
                'empty_body' => 'When a new note is published, it will appear here from the connected journal source.',
                'empty_cta_label' => 'Ask for updates',
                'empty_cta_url' => '/contact',
            ],
            'courses' => [
                'empty_index' => 'COURSES',
                'empty_heading' => 'The first course is being prepared.',
                'empty_body' => 'Join the conversation to hear when enrolment opens.',
                'empty_cta_label' => 'Join the conversation',
                'empty_cta_url' => '/contact',
            ],
        ];

        $homeId = DB::table('pages')->where('key', 'home')->value('id');
        if (! $homeId) return;

        foreach ($defaults as $key => $values) {
            $section = DB::table('page_sections')->where('page_id', $homeId)->where('key', $key)->first();
            if (! $section) continue;
            $payload = array_merge(json_decode($section->payload ?: '{}', true) ?: [], $values);
            DB::table('page_sections')->where('id', $section->id)->update([
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Keep values edited by administrators; these are content defaults, not runtime state.
    }
};
