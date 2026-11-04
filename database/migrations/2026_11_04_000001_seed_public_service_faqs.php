<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add reviewed, service-level answers without overwriting any existing FAQ
     * that an administrator may already have edited.
     */
    public function up(): void
    {
        $now = now();

        $courseId = DB::table('courses')->where('slug', 'forex-foundations')->value('id');
        if ($courseId) {
            $this->insertMissing('course_faqs', 'course_id', $courseId, [
                ['question' => 'What does Financial Academy teach?', 'answer' => 'Financial Academy introduces market structure, probability, systematic trading, risk planning and disciplined decision-making. The emphasis is on building a process before adding complexity.', 'sort_order' => 10],
                ['question' => 'Who is Financial Academy for?', 'answer' => 'It is for careful beginners and developing traders who want clearer foundations, written rules and a practical way to review decisions. Course availability is shown on each course page.', 'sort_order' => 20],
                ['question' => 'Does the Academy provide personal trading advice or guaranteed returns?', 'answer' => 'No. The Academy provides general financial education and does not provide personalised financial advice, trade instructions or guarantees of profit. You remain responsible for your own decisions and risk.', 'sort_order' => 30],
            ], $now);
        }

        $pageFaqs = [
            'development' => [
                ['question' => 'What kind of digital software projects do you take on?', 'answer' => 'We work on clear, useful digital products such as websites, portals, dashboards, operational systems and integrations. The right scope depends on the workflow, users and information the project needs to support.', 'sort_order' => 10],
                ['question' => 'Can you improve an existing website or business workflow?', 'answer' => 'Yes. We can review an existing system, identify the parts that create friction and agree a focused improvement plan. Any work, integrations and handover requirements are confirmed in the project scope.', 'sort_order' => 20],
                ['question' => 'How do Digital Software projects move from idea to delivery?', 'answer' => 'We start with the brief and the work behind it, then shape the scope, build in visible stages, test important paths and prepare the agreed handover. Delivery dates and ongoing support are confirmed per project.', 'sort_order' => 30],
            ],
            'studio' => [
                ['question' => 'What does Creative Studio offer?', 'answer' => 'Creative Studio offers purposeful photography, videography, event coverage, editing and selected print or photobook work. The suitable format depends on the story, people, place and intended use.', 'sort_order' => 10],
                ['question' => 'How do you plan a photography or video shoot?', 'answer' => 'We clarify the brief, audience, mood and practical requirements, then plan the location, schedule, people, shot list and delivery formats before production begins.', 'sort_order' => 20],
                ['question' => 'How do I request a Creative Studio quote?', 'answer' => 'Send an enquiry with the service you are considering, the occasion or project, preferred date, location and any delivery requirements. We review the brief and return with suitable next steps and a custom quote.', 'sort_order' => 30],
            ],
            'faqs' => [
                ['question' => 'How do I contact Mwanafunzi Investor about a question not covered here?', 'answer' => 'Use the contact page and choose the relevant area—Financial Academy, Digital Software or Creative Studio. Include enough context for the team to understand what you need and where the question belongs.', 'sort_order' => 10],
            ],
        ];

        foreach ($pageFaqs as $key => $faqs) {
            $pageId = DB::table('pages')->where('key', $key)->value('id');
            if ($pageId) {
                $this->insertMissing('faqs', 'faqable_id', $pageId, $faqs, $now, [
                    'faqable_type' => Page::class,
                ]);
            }
        }
    }

    /** @param array<int, array{question:string,answer:string,sort_order:int}> $faqs */
    private function insertMissing(string $table, string $foreignKey, int $foreignId, array $faqs, $now, array $extra = []): void
    {
        foreach ($faqs as $faq) {
            $where = array_merge($extra, [$foreignKey => $foreignId, 'question' => $faq['question']]);
            if (DB::table($table)->where($where)->exists()) {
                continue;
            }

            DB::table($table)->insert(array_merge($where, [
                'answer' => $faq['answer'],
                'sort_order' => $faq['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        // Deliberately non-destructive: administrators may edit these answers
        // after deployment, so rollback must not remove content unexpectedly.
    }
};
