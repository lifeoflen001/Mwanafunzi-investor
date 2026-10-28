<?php

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->section('home', 'divisions', 'split_content', 'Choose where the work takes you.', 'One brand, three focused divisions. Start with the part of the work that matters most to you now.', [
            'eyebrow' => 'The Mwanafunzi ecosystem',
            'anchor_id' => 'divisions',
        ], 20);

        $this->section('development', 'selected-work', 'feature_grid', 'Projects built for real work.', 'Systems and digital products designed around real workflows, operational needs and business problems.', [
            'eyebrow' => 'Selected work',
            'cta_label' => 'View all projects',
            'cta_url' => '/digital-software/projects',
            'anchor_id' => 'selected-work',
        ], 15);
        $this->section('development', 'hospitality', 'feature_grid', 'Websites and operational systems for hospitality businesses.', 'Professional guest-facing websites and maintainable systems for hotels, lodges, safari camps and tour operators.', [
            'eyebrow' => 'Hospitality & Tourism',
            'anchor_id' => 'hospitality',
            'cards' => [
                ['index' => '01', 'title' => 'Guest-facing websites', 'body' => 'Accommodation, gallery, enquiry and contact journeys that remain clear on mobile.', 'tags' => ''],
                ['index' => '02', 'title' => 'Booking and enquiry UX', 'body' => 'Useful paths for availability questions, direct enquiries and WhatsApp conversations.', 'tags' => ''],
                ['index' => '03', 'title' => 'Hotel operations', 'body' => 'Reservations, room planning, POS, finance, inventory and reporting where the data exists.', 'tags' => ''],
                ['index' => '04', 'title' => 'Tour operations', 'body' => 'Inquiries, quotations, itineraries, bookings, suppliers and operational handoffs.', 'tags' => ''],
            ],
        ], 25);
        $this->section('development', 'industries', 'split_content', 'Selected industries.', 'The catalogue reflects the industries represented by published project records.', [
            'eyebrow' => 'Areas of work',
            'anchor_id' => 'industries',
        ], 45);

        $this->section('studio', 'selected-work', 'feature_grid', 'Stories worth keeping.', 'Selected work made with real people, real constraints and a clear purpose.', [
            'eyebrow' => 'Selected work',
            'cta_label' => 'View studio projects',
            'cta_url' => '/creative-studio/projects',
            'anchor_id' => 'projects',
        ], 25);

        $this->section('learn', 'academy-framework', 'framework', 'The Student of Money Framework.', 'Good decisions become easier when they have somewhere to live.', [
            'eyebrow' => 'A process you can come back to',
            'steps' => [
                ['title' => 'Learn', 'body' => 'Understand the market and your own assumptions.'],
                ['title' => 'Define Rules', 'body' => 'Write down what must be true before you act.'],
                ['title' => 'Plan Risk', 'body' => 'Decide what you can responsibly put at risk.'],
                ['title' => 'Execute', 'body' => 'Follow the plan without negotiating with fear.'],
                ['title' => 'Journal', 'body' => 'Capture the decision while the context is clear.'],
                ['title' => 'Review', 'body' => 'Look for patterns across a meaningful sample.'],
                ['title' => 'Improve', 'body' => 'Make one thoughtful change at a time.'],
            ],
            'anchor_id' => 'academy-framework',
        ], 50);

        $this->section('projects', 'case-studies', 'split_content', 'Work with a point of view.', 'Each project is shown with the context available: what needed to change, what was built and what the work made possible.', [
            'eyebrow' => 'Case studies',
            'anchor_id' => 'case-studies',
        ], 10);
        $this->section('projects', 'empty-state', 'rich_text', 'Projects are being prepared.', 'Case studies will appear here as the work is documented and published.', [
            'eyebrow' => 'Selected work',
        ], 20);

        $this->cta('development', 'selected-work', 'View all projects', '/digital-software/projects');
        $this->cta('development', 'final_cta', 'Discuss a project', '/digital-software/contact');
        $this->cta('studio', 'selected-work', 'View studio projects', '/creative-studio/projects');
        $this->cta('studio', 'final_cta', 'Book a shoot', '/creative-studio/book');
    }

    public function down(): void
    {
        foreach (['home' => ['divisions'], 'development' => ['selected-work', 'hospitality', 'industries'], 'studio' => ['selected-work'], 'learn' => ['academy-framework'], 'projects' => ['case-studies', 'empty-state']] as $pageKey => $keys) {
            if ($page = Page::where('key', $pageKey)->first()) {
                $page->sections()->whereIn('key', $keys)->delete();
            }
        }
    }

    private function section(string $pageKey, string $key, string $type, string $heading, string $body, array $payload, int $order): void
    {
        $page = Page::where('key', $pageKey)->first();
        if (! $page) {
            return;
        }

        PageSection::firstOrCreate(
            ['page_id' => $page->id, 'key' => $key],
            [
                'section_type' => $type,
                'heading' => $heading,
                'body' => $body,
                'payload' => $payload,
                'sort_order' => $order,
                'is_enabled' => true,
            ],
        );
    }

    private function cta(string $pageKey, string $key, string $label, string $url): void
    {
        $page = Page::where('key', $pageKey)->first();
        $section = $page?->sections()->where('key', $key)->first();
        if (! $section) {
            return;
        }

        $section->update([
            'cta_label' => $section->cta_label ?: $label,
            'cta_url' => $section->cta_url ?: $url,
        ]);
    }
};
