<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('support_heading')->nullable()->after('hero_alignment');
            $table->text('support_copy')->nullable()->after('support_heading');
            $table->date('effective_date')->nullable()->after('support_copy');
        });

        $this->updatePage('about', [
            'hero_eyebrow' => 'About Mwanafunzi Investor',
            'hero_title' => 'Become trustworthy with the decisions in front of you.',
            'hero_summary' => 'Mwanafunzi means student. The name is a reminder that the work is continuous: learn, practise, review and improve.',
        ]);

        $this->updatePage('student-of-money', [
            'hero_eyebrow' => 'The signature philosophy',
            'hero_title' => 'Always a',
            'hero_highlight' => 'student of money.',
            'hero_summary' => 'Continuous learning. Probability over prediction. Process over outcome. Stewardship over shortcuts.',
        ]);

        $this->updatePage('learn', [
            'hero_title' => 'Learn to build a process that can hold up under uncertainty.',
            'hero_summary' => 'Four practical entry points for replacing noise with a calm, structured way of thinking about markets.',
        ]);

        $this->updatePage('contact', [
            'hero_title' => 'Start a conversation.',
            'hero_summary' => 'Tell us what you are working on, what you want to learn or which Mwanafunzi Investor module you want to reach.',
        ]);

        $legal = [
            'privacy-policy' => ['eyebrow' => 'Policies / Privacy', 'title' => 'Privacy policy', 'summary' => 'We collect only the information needed to respond to enquiries, operate accounts and orders, and improve this platform.', 'support_heading' => 'Questions about your information?', 'support_copy' => 'If you have a question about a contact submission, account record or order record, contact the Mwanafunzi Investor desk.'],
            'terms' => ['eyebrow' => 'Policies / Terms', 'title' => 'Terms of use', 'summary' => 'This website provides educational information and tools in development. By using it, you agree to use the content responsibly and understand that availability may change.', 'support_heading' => 'Need clarity before you rely on a term?', 'support_copy' => 'If a term or a course, product or account condition is unclear, contact the Mwanafunzi Investor desk before relying on it.'],
            'risk-disclosure' => ['eyebrow' => 'Policies / Risk', 'title' => 'Risk disclosure', 'summary' => 'Mwanafunzi Investor content is for educational purposes only and is not personalised financial advice. Trading involves risk, and leveraged trading can result in the loss of capital.', 'support_heading' => 'Understand the risk before you trade.', 'support_copy' => 'Questions about this disclosure? Contact the Mwanafunzi Investor desk before using educational material or tools.'],
            'refund-policy' => ['eyebrow' => 'Policies / Refunds', 'title' => 'Refund policy', 'summary' => 'Products and courses will publish clear purchase and refund terms before enrolment or checkout is enabled. Until then, availability is marked as coming soon or waitlist.', 'support_heading' => 'Clear purchase and refund expectations.', 'support_copy' => 'For a payment or access question, contact the Mwanafunzi Investor desk with the relevant order details.'],
            'disclaimer' => ['eyebrow' => 'Policies / Disclaimer', 'title' => 'Educational disclaimer', 'summary' => 'Nothing on this website should be understood as a recommendation to buy or sell an asset. Consider your own circumstances and seek independent professional advice where appropriate.', 'support_heading' => 'Questions about the scope of this content?', 'support_copy' => 'Contact the Mwanafunzi Investor desk if you need clarification about an educational page or tool.'],
        ];
        foreach ($legal as $key => $data) {
            $this->updatePage($key, [
                'hero_eyebrow' => $data['eyebrow'],
                'hero_title' => $data['title'],
                'hero_summary' => $data['summary'],
                'support_heading' => $data['support_heading'],
                'support_copy' => $data['support_copy'],
                'effective_date' => now()->toDateString(),
            ]);
        }

        $this->upsertSection('learn', [
            'key' => 'principle', 'section_type' => 'split_content', 'heading' => 'Always a mwanafunzi.',
            'body' => 'Learning is not a race to certainty. It is the practice of making better decisions with the information and risk in front of you.',
            'cta_label' => 'Read the philosophy', 'cta_url' => '/student-of-money',
            'payload' => ['eyebrow' => 'The principle'], 'sort_order' => 20,
        ]);

        $this->upsertSection('contact', [
            'key' => 'intro', 'payload' => ['eyebrow' => 'The desk is open', 'list' => [
                'Learning paths, courses and tools', 'Digital systems and website projects', 'Photography and videography work', 'Partnerships and general questions',
            ]],
        ]);

        $this->upsertSection('student-of-money', [
            'key' => 'intro', 'body' => 'A Student of Money does not need to be right on every trade. They need controlled losses, repeatable execution and an edge that can express itself across a meaningful sample.\n\nThey stay curious, write down what must be true before acting, protect capital, review honestly and make one thoughtful change at a time.',
        ]);

        $this->upsertSection('student-of-money', [
            'key' => 'framework', 'section_type' => 'framework', 'heading' => 'The framework.', 'body' => 'A process you can come back to.',
            'payload' => ['eyebrow' => 'A process you can come back to', 'steps' => [
                ['title' => 'Learn', 'body' => 'Understand the market and your assumptions.'],
                ['title' => 'Define Rules', 'body' => 'Write down what must be true before you act.'],
                ['title' => 'Plan Risk', 'body' => 'Decide what you can responsibly put at risk.'],
                ['title' => 'Execute', 'body' => 'Follow the plan without negotiating with fear.'],
                ['title' => 'Journal', 'body' => 'Capture the decision while the context is clear.'],
                ['title' => 'Review', 'body' => 'Look for patterns across a meaningful sample.'],
                ['title' => 'Improve', 'body' => 'Make one thoughtful change at a time.'],
            ]], 'sort_order' => 20,
        ]);
    }

    private function updatePage(string $key, array $values): void
    {
        $values['updated_at'] = now();
        DB::table('pages')->where('key', $key)->update($values);
    }

    private function upsertSection(string $pageKey, array $values): void
    {
        $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
        if (! $pageId) return;

        $key = $values['key'];
        unset($values['key']);
        if (array_key_exists('payload', $values)) {
            $values['payload'] = json_encode($values['payload'], JSON_UNESCAPED_UNICODE);
        }
        DB::table('page_sections')->updateOrInsert(
            ['page_id' => $pageId, 'key' => $key],
            array_merge(['section_type' => 'rich_text', 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()], $values, ['page_id' => $pageId])
        );
    }

    public function down(): void
    {
        foreach (['learn', 'student-of-money'] as $pageKey) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if ($pageId) DB::table('page_sections')->where('page_id', $pageId)->whereIn('key', ['principle', 'framework'])->delete();
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['support_heading', 'support_copy', 'effective_date']);
        });
    }
};
