<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->mergePayload('home', 'philosophy', [
            'cards' => [
                ['index' => '01', 'title' => 'Student of Money', 'body' => 'Stay curious. Study the relationships behind the price.', 'tags' => ''],
                ['index' => '02', 'title' => 'Systems over signals', 'body' => 'Turn ideas into rules you can test, repeat and review.', 'tags' => ''],
                ['index' => '03', 'title' => 'Probability + discipline', 'body' => 'Respect the sample size, the risk and the work.', 'tags' => ''],
            ],
            'visual_caption' => 'Study the process',
            'visual_index' => '01',
        ]);
        $this->mergePayload('home', 'probability', [
            'secondary_cta_label' => 'Learn risk management',
            'secondary_cta_url' => '#contact',
            'dashboard_title' => 'PROCESS DASHBOARD',
            'dashboard_status' => 'No results fabricated',
            'metrics' => [
                ['label' => 'Risk per trade', 'value' => 'Defined'],
                ['label' => 'Expected R', 'value' => 'Tested'],
                ['label' => 'Sample size', 'value' => 'Respected'],
                ['label' => 'Execution quality', 'value' => 'Reviewed'],
            ],
        ]);
        $this->mergePayload('home', 'final_cta', ['secondary_cta_label' => 'Explore Tools', 'secondary_cta_url' => '#tools']);
        $this->mergePayload('home', 'tools', ['card_cta_label' => 'View tool']);
        $this->mergePayload('home', 'learning', ['card_cta_label' => 'Learn more']);
        $this->mergePayload('home', 'journal', ['card_cta_label' => 'Read the note', 'empty_cta_label' => 'Ask for updates']);
        $this->mergePayload('home', 'courses', ['empty_cta_label' => 'Join the conversation']);
    }

    private function mergePayload(string $pageKey, string $sectionKey, array $values): void
    {
        $section = DB::table('page_sections')->join('pages', 'pages.id', '=', 'page_sections.page_id')->where('pages.key', $pageKey)->where('page_sections.key', $sectionKey)->select('page_sections.id', 'page_sections.payload')->first();
        if (! $section) return;
        $payload = array_merge(json_decode($section->payload ?: '{}', true) ?: [], $values);
        DB::table('page_sections')->where('id', $section->id)->update(['payload' => json_encode($payload, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Payload keys are intentionally retained if an administrator has edited them.
    }
};
