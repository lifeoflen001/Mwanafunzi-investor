<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('business_units')->where('slug', 'development')->update([
            'name' => 'Digital Software',
            'tagline' => 'Websites and software built for real work.',
            'route_name' => 'digital-systems',
            'updated_at' => $now,
        ]);

        DB::table('pages')->where('key', 'development')->update([
            'name' => 'Digital Software',
            'hero_eyebrow' => 'Mwanafunzi Investor / Digital Software',
            'hero_title' => 'Software for work that needs to',
            'hero_highlight' => 'move.',
            'hero_summary' => 'We design and build clear, dependable websites, products and operational software for organisations that are ready to work with less friction.',
            'seo_title' => 'Digital Software — Mwanafunzi Investor',
            'seo_description' => 'Websites, software products, dashboards and integrations built around real business needs.',
            'updated_at' => $now,
        ]);

        DB::table('team_members')->where('department', 'Digital Systems & Creative Studio')->update([
            'department' => 'Digital Software & Creative Studio',
            'updated_at' => $now,
        ]);

        $this->upsertSections('development', [
            ['key' => 'products', 'section_type' => 'feature_grid', 'heading' => 'Products that make the next step clearer.', 'body' => 'From public websites to internal platforms, each product is shaped around the people, decisions and workflows it needs to support.', 'payload' => ['eyebrow' => 'What we deliver', 'cards' => [
                ['index' => '01', 'title' => 'Websites and landing pages', 'body' => 'Fast, focused public websites that explain the offer clearly and help the right people take the next step.', 'tags' => 'Strategy · UX · Build'],
                ['index' => '02', 'title' => 'Client and customer portals', 'body' => 'Secure workspaces for accounts, purchases, downloads, learning and the everyday relationship with your customers.', 'tags' => 'Accounts · Commerce · Access'],
                ['index' => '03', 'title' => 'Operational software', 'body' => 'Dashboards, admin desks and workflow tools that replace repeated manual work with a system people can trust.', 'tags' => 'Workflows · Data · Teams'],
                ['index' => '04', 'title' => 'Commerce and integrations', 'body' => 'Practical payment, content and business integrations that connect the important parts of the operation.', 'tags' => 'Payments · APIs · Automation'],
            ]], 'sort_order' => 15],
            ['key' => 'languages', 'section_type' => 'feature_grid', 'heading' => 'Built with tools chosen for the work.', 'body' => 'The stack follows the problem. We use dependable, maintainable technologies that keep products understandable after launch.', 'payload' => ['eyebrow' => 'Languages and technologies', 'cards' => [
                ['index' => '01', 'title' => 'PHP and Laravel', 'body' => 'Reliable server-side applications, authentication, content management and business workflows.', 'tags' => 'PHP · Laravel · MySQL'],
                ['index' => '02', 'title' => 'JavaScript and interfaces', 'body' => 'Responsive interactions and clear frontend experiences for people using the system every day.', 'tags' => 'JavaScript · Vite · CSS'],
                ['index' => '03', 'title' => 'APIs and integrations', 'body' => 'Connect payments, communications, data and third-party services without losing control of the core product.', 'tags' => 'REST · Webhooks · Services'],
                ['index' => '04', 'title' => 'Deployment and care', 'body' => 'Production-minded delivery, performance checks, backups and support after the first release.', 'tags' => 'Hosting · Security · Support'],
            ]], 'sort_order' => 25],
            ['key' => 'testimonials', 'section_type' => 'quote', 'heading' => 'The work should earn trust.', 'body' => 'Client perspectives are published only when they have been supplied and approved. Explore the current project feedback below.', 'payload' => ['eyebrow' => 'Client perspective'], 'sort_order' => 35],
        ]);

        $this->upsertSections('studio', [
            ['key' => 'services', 'section_type' => 'feature_grid', 'heading' => 'Photography and video for the moments that matter.', 'body' => 'We make useful, considered visual work for people, brands, organisations and occasions — from the first brief to the final print or export.', 'payload' => ['eyebrow' => 'What we do', 'cards' => [
                ['index' => '01', 'title' => 'Brand photography', 'body' => 'Purposeful images for products, teams, campaigns and businesses that need a clear visual point of view.', 'tags' => 'Brands · Products · Teams'],
                ['index' => '02', 'title' => 'Indoor and outdoor photography', 'body' => 'Portraits, places, architecture, products and documentary frames made with attention to light and context.', 'tags' => 'Portraits · Places · Products'],
                ['index' => '03', 'title' => 'Parties and events', 'body' => 'Candid and organised coverage for parties, celebrations, launches, gatherings and the details people want to remember.', 'tags' => 'Events · Parties · Documentary'],
                ['index' => '04', 'title' => 'Videography and editing', 'body' => 'Short films, interviews, event films and social edits that communicate clearly and keep the human moment intact.', 'tags' => 'Video · Interviews · Editing'],
                ['index' => '05', 'title' => 'Photobooks and printing', 'body' => 'Selected images turned into photobooks, prints and physical pieces that can be held, shared and kept.', 'tags' => 'Photobooks · Prints · Delivery'],
                ['index' => '06', 'title' => 'Creative production support', 'body' => 'Pre-production, shot lists, locations, post-production and organised delivery for a smoother finished result.', 'tags' => 'Planning · Colour · Exports'],
            ]], 'sort_order' => 10],
            ['key' => 'formats', 'section_type' => 'feature_grid', 'heading' => 'One story, delivered in the right form.', 'body' => 'The finished work can live on a screen, in a campaign, in a frame or in a book. We plan for how you will actually use it.', 'payload' => ['eyebrow' => 'Formats', 'cards' => [
                ['index' => '01', 'title' => 'Social and digital', 'body' => 'Short-form video, campaign images and web-ready assets sized for the channels where your audience already is.', 'tags' => 'Social · Web · Campaigns'],
                ['index' => '02', 'title' => 'Print and photobooks', 'body' => 'Carefully selected and prepared images for albums, photobooks, wall prints and meaningful gifts.', 'tags' => 'Books · Albums · Prints'],
                ['index' => '03', 'title' => 'Events and archives', 'body' => 'A clear, organised record of the people, atmosphere and small details that should not disappear.', 'tags' => 'Parties · Events · Archives'],
            ]], 'sort_order' => 25],
            ['key' => 'testimonials', 'section_type' => 'quote', 'heading' => 'Good work should feel like care.', 'body' => 'Client perspectives are published only when they have been supplied and approved. Explore the current project feedback below.', 'payload' => ['eyebrow' => 'Client perspective'], 'sort_order' => 35],
        ]);
    }

    private function upsertSections(string $pageKey, array $sections): void
    {
        $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
        if (! $pageId) return;

        foreach ($sections as $section) {
            DB::table('page_sections')->updateOrInsert(
                ['page_id' => $pageId, 'key' => $section['key']],
                [
                    'section_type' => $section['section_type'],
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                    'payload' => json_encode($section['payload'], JSON_UNESCAPED_UNICODE),
                    'sort_order' => $section['sort_order'],
                    'is_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('business_units')->where('slug', 'development')->update(['name' => 'Digital Systems', 'route_name' => 'development']);
        DB::table('pages')->where('key', 'development')->update(['name' => 'Digital Systems']);
        $pageIds = DB::table('pages')->whereIn('key', ['development', 'studio'])->pluck('id');
        DB::table('page_sections')->whereIn('page_id', $pageIds)->whereIn('key', ['products', 'languages', 'formats', 'testimonials'])->delete();
    }
};
