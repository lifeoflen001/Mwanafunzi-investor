<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('key', 'privacy-policy')->value('id');
        if (! $pageId) return;

        DB::table('page_sections')->updateOrInsert(
            ['page_id' => $pageId, 'key' => 'newsletter-subscriptions'],
            [
                'section_type' => 'rich_text', 'heading' => 'Newsletter subscriptions',
                'body' => 'If you subscribe to the Mwanafunzi Investor newsletter, we store your email address, subscription source and lifecycle timestamps so that we can send the requested updates and honour confirmations or unsubscribes. Double opt-in is enabled by default. Newsletter consent is separate from cookie consent, and accepting cookies never subscribes you to email updates.',
                'payload' => json_encode(['callout' => 'You can unsubscribe at any time using the link in a newsletter message.']), 'sort_order' => 45, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('key', 'privacy-policy')->value('id');
        if ($pageId) DB::table('page_sections')->where('page_id', $pageId)->where('key', 'newsletter-subscriptions')->delete();
    }
};
