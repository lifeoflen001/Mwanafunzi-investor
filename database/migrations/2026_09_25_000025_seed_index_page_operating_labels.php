<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'learn' => [
                'card_cta_label' => 'Explore the path',
                'empty_index' => 'LEARNING PATHS',
                'empty_heading' => 'The first lessons are being prepared.',
                'empty_body' => 'Check back soon for the first published learning path.',
            ],
            'courses' => [
                'empty_index' => 'COURSES',
                'empty_heading' => 'The first course is being prepared.',
                'empty_body' => 'Join the conversation to hear when enrolment opens.',
                'empty_cta_label' => 'Contact the desk',
                'empty_cta_url' => '/contact',
            ],
            'tools' => [
                'card_cta_label' => 'View tool',
                'empty_index' => 'TOOLS',
                'empty_heading' => 'The first tools are being prepared.',
                'empty_body' => 'Check back soon for deliberate practice tools.',
            ],
            'journal' => [
                'card_cta_label' => 'Read the note',
                'empty_index' => 'J / 00',
                'empty_heading' => 'The next entry is being studied.',
                'empty_body' => 'When a new note is published, it will appear here from the connected journal source.',
                'empty_cta_label' => 'Ask for updates',
                'empty_cta_url' => '/contact',
            ],
        ];

        foreach ($defaults as $pageKey => $pageDefaults) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if (! $pageId) continue;

            $section = DB::table('page_sections')->where('page_id', $pageId)->where('key', 'intro')->first();
            if (! $section) continue;

            $payload = json_decode($section->payload ?: '{}', true) ?: [];
            $payload = array_replace($pageDefaults, $payload);

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
