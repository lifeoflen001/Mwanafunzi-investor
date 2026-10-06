<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('key', 'about')->value('id');
        if (! $pageId) return;

        $story = DB::table('page_sections')->where(['page_id' => $pageId, 'key' => 'philosophy'])->first();
        if ($story) {
            $existing = json_decode($story->payload ?: '{}', true) ?: [];
            $payload = array_merge([
                'eyebrow' => 'Why this exists',
                'image_alt' => 'A considered workspace for learning, research and disciplined decision-making.',
                'image_focal_point' => 'center center',
                'who_heading' => 'Who we are',
                'who_body' => 'Mwanafunzi Investor is a financial education platform built around systematic thinking, probability, disciplined execution, risk management and continuous learning.',
                'story_heading' => 'Our story',
                'story_body' => 'The platform exists for people who want to understand markets without the hype. Student of Money is the philosophy behind the work: learn carefully, practise deliberately, review honestly and keep improving. Real updates. Real journey.',
                'quote_label' => 'Our philosophy',
                'quote' => '“The point is not perfection — it is becoming trustworthy with the decisions in front of you.”',
            ], $existing);

            DB::table('page_sections')->where('id', $story->id)->update([
                'section_type' => 'about_story',
                'image' => $story->image ?: 'images/hero-about.webp',
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }

        DB::table('page_sections')->updateOrInsert(
            ['page_id' => $pageId, 'key' => 'mission-vision'],
            [
                'section_type' => 'mission_vision',
                'heading' => 'A useful direction for the work.',
                'body' => null,
                'image' => null,
                'cta_label' => null,
                'cta_url' => null,
                'payload' => json_encode([
                    'mission_label' => 'Our mission',
                    'mission_heading' => 'Make disciplined thinking more accessible.',
                    'mission_body' => 'Help people build repeatable ways of thinking about markets, risk and money.',
                    'mission_enabled' => true,
                    'vision_label' => 'Our vision',
                    'vision_heading' => 'A trusted place to keep learning.',
                    'vision_body' => 'Build an education and tools platform for people who want to become better decision-makers, not just better predictors.',
                    'vision_enabled' => true,
                ], JSON_UNESCAPED_UNICODE),
                'sort_order' => 15,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('key', 'about')->value('id');
        if (! $pageId) return;
        DB::table('page_sections')->where(['page_id' => $pageId, 'key' => 'mission-vision'])->delete();
        DB::table('page_sections')->where(['page_id' => $pageId, 'key' => 'philosophy'])->update(['section_type' => 'rich_text', 'updated_at' => now()]);
    }
};
