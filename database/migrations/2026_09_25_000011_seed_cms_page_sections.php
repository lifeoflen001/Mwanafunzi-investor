<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $sections = [
            'about' => [
                ['key' => 'philosophy', 'section_type' => 'rich_text', 'heading' => 'A different kind of market education.', 'body' => 'Mwanafunzi Investor exists for people who want to understand markets without the hype. The approach is grounded in systematic trading, probability, risk-first thinking and the humility to keep learning.\n\nThis is a real journey philosophy: no invented results, fictional credentials or promises of easy outcomes. The work is to build a process that can hold up when certainty is impossible.', 'payload' => ['eyebrow' => 'Why this exists', 'pull' => '“The point is not perfection — it is becoming trustworthy with the decisions in front of you.”'], 'sort_order' => 10],
                ['key' => 'approach', 'section_type' => 'split_content', 'heading' => 'Risk first. Process always.', 'body' => 'We study the relationships behind price, turn ideas into rules, respect sample size and make the cost of being wrong visible before acting. That is education for the long game.', 'cta_label' => 'Read Student of Money', 'cta_url' => '/student-of-money', 'payload' => ['eyebrow' => 'The approach', 'theme' => 'dark'], 'sort_order' => 20],
            ],
            'privacy-policy' => [
                ['key' => 'information-collected', 'heading' => 'Information collected', 'body' => 'We collect information that is needed to operate the platform, respond to a request or provide a product or course.', 'payload' => ['id' => 'information-collected', 'list' => ['Account details such as your name and email address when you register.', 'Contact details and the message you choose to send through the contact form.', 'Order, entitlement and payment-reference information connected to a purchase or enrolment.', 'Session and security information needed for sign-in, verification and basic site operation.']], 'sort_order' => 10],
                ['key' => 'contact-submissions', 'heading' => 'Contact submissions', 'body' => 'A contact message is used to understand and respond to the enquiry you submitted. It may be routed internally to the relevant Mwanafunzi Investor module so the right desk can follow up.', 'payload' => ['id' => 'contact-submissions', 'callout' => 'Contact messages are not sold. Please do not include passwords, payment-card numbers or other information that is not needed for your enquiry.'], 'sort_order' => 20],
                ['key' => 'orders-and-payments', 'heading' => 'Orders and payment data', 'body' => 'When commerce is enabled, the platform keeps an order record, product or course snapshot, payment status and provider reference so that access, support and reconciliation can work. Payment-card details are handled by the configured payment provider rather than stored as plain card data by this site.', 'payload' => ['id' => 'orders-and-payments'], 'sort_order' => 30],
                ['key' => 'cookies-and-sessions', 'heading' => 'Cookies and sessions', 'body' => 'The application uses essential cookies and sessions for authentication, security, form protection and remembering the state of a request. If optional analytics or marketing tools are added later, this policy should be updated before they are used.', 'payload' => ['id' => 'cookies-and-sessions'], 'sort_order' => 40],
                ['key' => 'use-and-sharing', 'heading' => 'How information is used and shared', 'body' => 'Information is used to provide the requested platform function, respond to support, process orders, protect the site and improve the clarity of the service. Access is limited to people or providers who need it for those purposes, including configured payment or email services. We do not sell contact information.', 'payload' => ['id' => 'use-and-sharing'], 'sort_order' => 50],
                ['key' => 'retention-and-security', 'heading' => 'Retention and security', 'body' => 'Records are kept for as long as they are needed for the purpose they were collected, account administration, support, financial reconciliation or a legitimate security need. The platform uses authentication, validation, access controls and private download routes, but no internet service can promise perfect security.', 'payload' => ['id' => 'retention-and-security'], 'sort_order' => 60],
                ['key' => 'your-questions', 'heading' => 'Your questions and requests', 'body' => 'You can contact the desk to ask what information is associated with your account or submission, request a correction, or ask a question about how information is handled. We will review the request in the context of the service and applicable requirements.', 'payload' => ['id' => 'your-questions'], 'sort_order' => 70],
            ],
            'terms' => [
                ['key' => 'acceptance', 'heading' => 'Acceptance of terms', 'body' => 'This website provides educational information, learning material and tools in development. By using it, you agree to use the platform responsibly and to follow these terms. If you do not agree, please do not use the platform.', 'sort_order' => 10],
                ['key' => 'educational-content', 'heading' => 'Educational nature of content', 'body' => 'Mwanafunzi Investor content is general education about markets, probability, systems and risk. It is not personalised financial advice, an instruction to trade, or a promise of a particular outcome. You are responsible for deciding whether any information is suitable for your circumstances.', 'sort_order' => 20],
                ['key' => 'user-responsibilities', 'heading' => 'User responsibilities', 'body' => 'Use the platform lawfully, provide accurate information where an account or order requires it, and keep your sign-in details private. Do not use the service to interfere with its operation, misrepresent another person or attempt to access information that is not yours.', 'sort_order' => 30],
                ['key' => 'accounts', 'heading' => 'Account use', 'body' => 'Some course, product and download functions require a verified account. The account area is for the registered user and may show only the courses, orders and entitlements associated with that account.', 'sort_order' => 40],
                ['key' => 'digital-products', 'heading' => 'Digital products and course access', 'body' => 'A product or course page should state its availability, description and access conditions before checkout or enrolment. Access is tied to the order or enrolment record created by the platform and may be affected if an order is cancelled or refunded.', 'sort_order' => 50],
                ['key' => 'payments-and-refunds', 'heading' => 'Payments and refunds', 'body' => 'Payment status and provider references are recorded to support checkout and reconciliation. Refund questions are handled under the Refund Policy and any specific terms shown with the relevant product or course.', 'sort_order' => 60],
                ['key' => 'intellectual-property', 'heading' => 'Intellectual property', 'body' => 'The site structure, original writing, course material, tools, marks and other content belong to Mwanafunzi Investor or the relevant rights holder. You may use content for personal learning within the access granted to you, but do not copy, resell, redistribute or present it as your own without permission.', 'sort_order' => 70],
                ['key' => 'prohibited-use', 'heading' => 'Prohibited use', 'body' => 'Do not scrape private areas, bypass access controls, upload harmful code, misuse another user’s account, or use the platform to make misleading financial claims.', 'sort_order' => 80],
                ['key' => 'availability-and-changes', 'heading' => 'Availability and changes', 'body' => 'The platform, content and catalogue may change as the work develops. We may add, remove or revise material, pause a feature, or correct an error. Changes to these terms will be reflected on this page when they are made.', 'sort_order' => 90],
            ],
            'risk-disclosure' => [
                ['key' => 'educational-purpose', 'heading' => 'Educational purpose', 'body' => 'Mwanafunzi Investor provides general education about markets, probability, trading systems, risk planning and disciplined decision-making. Nothing on this site is personalised financial advice or a recommendation to buy or sell an asset.', 'sort_order' => 10],
                ['key' => 'general-trading-risk', 'heading' => 'General trading risk', 'body' => 'Trading and investing involve uncertainty. Prices can move against you, assumptions can be wrong, and a strategy that worked in one period may not work in another. You can lose part or all of the capital you put at risk.', 'sort_order' => 20],
                ['key' => 'leverage-and-capital-loss', 'heading' => 'Leveraged products and capital loss', 'body' => 'Leverage can increase both gains and losses and may cause losses to develop quickly. Only use capital you can responsibly afford to lose, and understand the rules, costs and risks of the platform or broker you choose.', 'sort_order' => 30],
                ['key' => 'no-guarantees', 'heading' => 'No guaranteed returns', 'body' => 'No return, level of income or trading result is guaranteed. Past performance, examples, case studies and hypothetical illustrations do not guarantee future results.', 'sort_order' => 40],
                ['key' => 'simulated-results', 'heading' => 'Simulated or hypothetical results', 'body' => 'A backtest, model, calculator or hypothetical example may not reflect real execution, liquidity, fees, slippage, taxes or the emotional pressure of live decisions. Treat such material as an educational illustration, not a prediction.', 'sort_order' => 50],
                ['key' => 'tools-and-calculators', 'heading' => 'Tools and calculators', 'body' => 'Tools are provided to help make a process visible. Check inputs, assumptions and outputs independently before relying on them. A calculator cannot decide whether a trade or investment is appropriate for you.', 'sort_order' => 60],
                ['key' => 'user-responsibility', 'heading' => 'User responsibility', 'body' => 'You are responsible for your own decisions, account security, position sizing, records and professional advice. Consider your financial circumstances and seek independent qualified advice where appropriate.', 'sort_order' => 70],
                ['key' => 'third-party-platforms', 'heading' => 'Third-party platforms', 'body' => 'Broker, exchange, payment, charting and other third-party services have their own terms, technology and risks. Mwanafunzi Investor does not control their availability, pricing, execution or data.', 'sort_order' => 80],
                ['key' => 'changes', 'heading' => 'Changes to this disclosure', 'body' => 'This disclosure may be updated as the platform, products and educational material develop. The current version is the one published on this page.', 'sort_order' => 90],
            ],
            'refund-policy' => [
                ['key' => 'before-purchase', 'heading' => 'Before purchase', 'body' => 'A product or course should publish its availability, description, price and access conditions before checkout or enrolment is enabled. If the relevant offer is marked coming soon or waitlist, a purchase is not being represented as available.', 'sort_order' => 10],
                ['key' => 'digital-products', 'heading' => 'Digital product purchases', 'body' => 'Digital products may provide access to a download or entitlement after a successful payment. Keep your order reference and contact the desk if the access shown in your account does not match the order record.', 'sort_order' => 20],
                ['key' => 'course-purchases', 'heading' => 'Course purchases and enrolment', 'body' => 'Course enrolment and access are linked to the course and order records created by the platform. Course-specific access conditions shown before checkout apply to that course.', 'sort_order' => 30],
                ['key' => 'duplicate-or-failed-payments', 'heading' => 'Duplicate or failed transactions', 'body' => 'If you see a duplicate payment, an incorrect status or a payment that appears to have failed after funds were taken, send the order reference and payment-provider reference to the desk. We will check the platform record and provider response.', 'sort_order' => 40],
                ['key' => 'eligibility', 'heading' => 'Refund questions and eligibility', 'body' => 'This policy does not invent a fixed refund window. Eligibility depends on the product or course terms shown at purchase, the state of delivery or access, the order record and the circumstances described to support.', 'sort_order' => 50],
                ['key' => 'access-after-refund', 'heading' => 'Access after a confirmed refund', 'body' => 'Where a refund is confirmed, associated entitlements or course access may be revoked so that the account record matches the completed transaction.', 'sort_order' => 60],
                ['key' => 'processing', 'heading' => 'Processing and provider timelines', 'body' => 'The platform can record a refund request and its status, but the time for funds to appear can depend on the payment provider and the user’s bank. Provider delays do not change the order record held by the site.', 'sort_order' => 70],
                ['key' => 'support', 'heading' => 'Contact support', 'body' => 'Include your name, account email, order number, payment reference and a short description of the issue. Do not send full card numbers or passwords.', 'sort_order' => 80],
            ],
            'disclaimer' => [
                ['key' => 'educational-only', 'heading' => 'Educational content only', 'body' => 'Mwanafunzi Investor publishes educational material about markets, systems, probability, risk and disciplined decision-making. It is intended to support learning, not to replace individual advice.', 'sort_order' => 10],
                ['key' => 'not-advice', 'heading' => 'Not financial advice or a recommendation', 'body' => 'Nothing on this website should be understood as a recommendation to buy or sell an asset, open an account or use a particular provider.', 'sort_order' => 20],
                ['key' => 'market-uncertainty', 'heading' => 'Market uncertainty', 'body' => 'Markets are uncertain. Information can become outdated, examples can be incomplete and an approach that suits one person may not suit another. No outcome is guaranteed.', 'sort_order' => 30],
                ['key' => 'tools', 'heading' => 'Tools and calculators', 'body' => 'Tools and calculators are educational aids. Check the inputs, formulas, assumptions and outputs yourself, and do not treat a result as a promise or instruction.', 'sort_order' => 40],
                ['key' => 'third-party-services', 'heading' => 'Third-party data and services', 'body' => 'Links, charts, payment providers, brokers and other third-party services may have separate terms, data and risks. Mwanafunzi Investor is not responsible for changes or failures outside its control.', 'sort_order' => 50],
                ['key' => 'your-decision', 'heading' => 'Your decision responsibility', 'body' => 'You remain responsible for your decisions, account security, risk, records and any professional advice you choose to obtain. Consider your own circumstances before acting.', 'sort_order' => 60],
                ['key' => 'changes', 'heading' => 'Changes', 'body' => 'This disclaimer may be updated as the platform and its content develop. The current version is the one published on this page.', 'sort_order' => 70],
            ],
        ];

        foreach ($sections as $pageKey => $pageSections) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if (! $pageId) {
                continue;
            }

            foreach ($pageSections as $section) {
                $section = array_merge(['section_type' => 'rich_text'], $section);
                $section['payload'] = isset($section['payload']) ? json_encode($section['payload'], JSON_UNESCAPED_UNICODE) : null;
                DB::table('page_sections')->updateOrInsert(
                    ['page_id' => $pageId, 'key' => $section['key']],
                    array_merge($section, ['page_id' => $pageId, 'is_enabled' => true, 'created_at' => $now, 'updated_at' => $now])
                );
            }
        }
    }

    public function down(): void
    {
        foreach (['about', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer'] as $pageKey) {
            $pageId = DB::table('pages')->where('key', $pageKey)->value('id');
            if ($pageId) {
                DB::table('page_sections')->where('page_id', $pageId)->delete();
            }
        }
    }
};
