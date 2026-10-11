<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $sections = [
            'privacy-policy' => [
                ['key' => 'rights-and-complaints', 'heading' => 'Your privacy questions and rights', 'body' => 'Depending on the applicable law and the context of a request, you may ask for access to, correction of or information about personal data associated with your account or submission, withdraw consent where processing relies on consent, or object to direct marketing. We may need to verify identity and assess the request against applicable retention or legal requirements. You can contact the Mwanafunzi Investor desk with a privacy request or complaint.', 'sort_order' => 72],
                ['key' => 'retention-by-category', 'heading' => 'Retention by data category', 'body' => 'Account and access records are retained while an account or entitlement is needed. Order, payment and receipt records may need to remain available for reconciliation, support and applicable accounting requirements. Newsletter records are retained while needed to honour subscription, confirmation and unsubscribe choices. Contact messages and comments are retained for support, moderation and security as needed. Exact retention periods are a business and legal decision and should be confirmed before production launch.', 'sort_order' => 74],
                ['key' => 'international-processing', 'heading' => 'Service providers and international processing', 'body' => 'Configured email, payment, hosting and abuse-prevention providers may process information outside the user\'s country. The provider inventory and deployment configuration must be reviewed whenever an integration changes. Cross-border transfer safeguards and any registration or notification requirements are subject to jurisdiction-specific legal review.', 'sort_order' => 76],
            ],
            'terms' => [
                ['key' => 'comments-and-user-content', 'heading' => 'Comments and user-submitted content', 'body' => 'Comments are submitted by authenticated users, moderated before publication and displayed with the account name or initials configured by the platform. Do not submit personal information, confidential material, passwords or payment details. We may remove content that is unlawful, harmful, abusive, misleading or unrelated to the discussion. Questions about copyright permission or removal should be sent to the desk.', 'sort_order' => 75],
                ['key' => 'third-party-services', 'heading' => 'Third-party services', 'body' => 'Payment, email, hosting, spam-prevention and linked services may have separate terms and privacy notices. Their availability and processing are outside the direct control of Mwanafunzi Investor. Review the relevant provider information before using a linked service or completing a transaction.', 'sort_order' => 85],
            ],
        ];

        foreach ($sections as $pageKey => $pageSections) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if (! $pageId) continue;
            foreach ($pageSections as $section) {
                DB::table('page_sections')->updateOrInsert(
                    ['page_id' => $pageId, 'key' => $section['key']],
                    array_merge($section, ['section_type' => 'rich_text', 'is_enabled' => true, 'created_at' => $now, 'updated_at' => $now])
                );
            }
        }
    }

    public function down(): void
    {
        foreach (['privacy-policy' => ['rights-and-complaints', 'retention-by-category', 'international-processing'], 'terms' => ['comments-and-user-content', 'third-party-services']] as $pageKey => $keys) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if ($pageId) DB::table('page_sections')->where('page_id', $pageId)->whereIn('key', $keys)->delete();
        }
    }
};
