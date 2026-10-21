<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleReaction;
use App\Models\BusinessUnit;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseFaq;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\ProductVersion;
use App\Models\Project;
use App\Models\Service;
use App\Models\StudioInquiry;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Safe, repeatable local/staging fixture data for manual workflow audits.
 *
 * Run explicitly with:
 *   php artisan db:seed --class=DevelopmentDemoSeeder
 *
 * This seeder intentionally refuses production and never deletes existing rows.
 * Every fixture is visibly marked with a DEMO prefix, an example.test address,
 * a draft/unpublished status, or a local-only operational state.
 */
class DevelopmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'staging'])) {
            throw new RuntimeException('DevelopmentDemoSeeder is limited to local, testing, and staging environments.');
        }

        $now = now();
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $academy = BusinessUnit::where('slug', 'forex')->firstOrFail();
        $development = BusinessUnit::where('slug', 'development')->firstOrFail();
        $demoClient = User::updateOrCreate(['email' => 'demo.client@mwanafunziinvestor.test'], [
            'name' => 'DEMO Client — Asha Example',
            'password' => 'DemoOnly!2026',
            'is_admin' => false,
            'status' => 'active',
            'phone' => '+255700000001',
            'country' => 'TZ',
            'email_verified_at' => $now,
        ]);
        $demoAdmin = User::updateOrCreate(['email' => 'demo.admin@mwanafunziinvestor.test'], [
            'name' => 'DEMO Admin — Local QA',
            'password' => 'DemoOnly!2026',
            'is_admin' => true,
            'status' => 'active',
            'email_verified_at' => $now,
        ]);

        $services = collect([
            ['title' => '[DEMO] Editorial portrait session', 'slug' => 'demo-editorial-portrait-session', 'short_description' => 'Local fixture for testing studio service discovery and enquiry routing.', 'detailed_description' => 'DEMO DATA ONLY. A fictional service used to exercise the Creative Studio catalog.', 'business_unit_id' => $studio->id, 'pricing_label' => 'Demo quote required', 'currency' => 'TZS', 'pricing_type' => 'custom_quote', 'delivery_estimate' => 'Demo estimate', 'included_features' => ['Demo briefing', 'Demo capture plan', 'Demo delivery review'], 'cta_label' => 'Book a shoot', 'sort_order' => 90, 'is_featured' => false, 'is_active' => false],
            ['title' => '[DEMO] Small event coverage', 'slug' => 'demo-small-event-coverage', 'short_description' => 'Local fixture for testing event-service browsing without publishing a real offer.', 'detailed_description' => 'DEMO DATA ONLY. No commercial availability or client commitment is implied.', 'business_unit_id' => $studio->id, 'pricing_label' => 'Demo quote required', 'currency' => 'TZS', 'pricing_type' => 'custom_quote', 'delivery_estimate' => 'Demo estimate', 'included_features' => ['Demo run sheet', 'Demo capture', 'Demo selection'], 'cta_label' => 'Book a shoot', 'sort_order' => 91, 'is_featured' => false, 'is_active' => false],
        ])->map(fn (array $data) => Service::updateOrCreate(['slug' => $data['slug']], $data));

        $demoProject = Project::updateOrCreate(['slug' => 'demo-studio-project'], [
            'title' => '[DEMO] Studio project for QA',
            'client_name' => 'DEMO Client — Example organisation',
            'industry' => 'Test fixture',
            'project_type' => 'Photography',
            'short_description' => 'A non-public project used to exercise project detail and gallery relationships.',
            'full_introduction' => 'DEMO DATA ONLY. This record is not a real client project.',
            'challenge' => 'Verify that project content can be created, related and rendered safely.',
            'role' => 'Local QA fixture.',
            'approach' => 'Use fictional records with explicit demo labels.',
            'solution' => 'Keep the record in draft until real permissioned work is available.',
            'status' => 'draft',
            'display_order' => 90,
            'is_featured' => false,
        ]);
        $demoProject->services()->syncWithoutDetaching($services->pluck('id'));

        foreach (['Photography', 'Videography', 'Events', 'Learning courses', 'Trading tools'] as $index => $type) {
            Testimonial::updateOrCreate(['client_name' => '[DEMO] Example '.$type, 'project_id' => $demoProject->id], [
                'client_role' => 'Fictional QA persona',
                'company' => 'DEMO organisation',
                'testimonial' => 'DEMO DATA ONLY. Concise sample feedback for '.$type.' workflow testing.',
                'project_id' => $demoProject->id,
                'rating' => 5,
                'consent_confirmed' => false,
                'consent_notes' => 'Fictional local fixture; never publish as a real endorsement.',
                'is_featured' => false,
                'is_published' => false,
                'sort_order' => 90 + $index,
            ]);
        }

        // Three clearly labelled local QA records per business module. These are
        // deliberately published only in local/staging fixture environments so
        // the public layout can be reviewed without presenting them as real
        // client endorsements.
        $moduleTestimonials = [
            ['module' => 'Financial Academy', 'business_unit_id' => $academy->id, 'project_id' => null, 'role' => 'Fictional learner persona', 'company' => 'DEMO organisation'],
            ['module' => 'Digital Software', 'business_unit_id' => $development->id, 'project_id' => null, 'role' => 'Fictional product persona', 'company' => 'DEMO organisation'],
            ['module' => 'Creative Studio', 'business_unit_id' => $studio->id, 'project_id' => $demoProject->id, 'role' => 'Fictional studio persona', 'company' => 'DEMO organisation'],
        ];

        foreach ($moduleTestimonials as $moduleIndex => $moduleData) {
            foreach (range(1, 3) as $position) {
                $clientName = '[DEMO] '.$moduleData['module'].' perspective '.$position;

                Testimonial::updateOrCreate(['client_name' => $clientName, 'business_unit_id' => $moduleData['business_unit_id']], [
                    'client_role' => $moduleData['role'],
                    'company' => $moduleData['company'],
                    'testimonial' => 'DEMO DATA ONLY. Placeholder '.$position.' for '.$moduleData['module'].' testimonial workflow testing.',
                    'project_id' => $moduleData['project_id'],
                    'business_unit_id' => $moduleData['business_unit_id'],
                    'rating' => null,
                    'consent_confirmed' => false,
                    'consent_notes' => 'Fictional local fixture; never publish as a real endorsement.',
                    'is_featured' => true,
                    'is_published' => true,
                    'sort_order' => 100 + ($moduleIndex * 10) + $position,
                ]);
            }
        }

        $demoMedia = Media::updateOrCreate(['path' => 'images/brand/mwanafunzi-logo-light.webp'], [
            'disk' => 'public',
            'filename' => 'mwanafunzi-logo-light.webp',
            'mime_type' => 'image/webp',
            'size' => 0,
            'alt_text' => 'Mwanafunzi Investor logo — existing brand asset',
            'title' => '[DEMO] Existing brand asset relationship fixture',
            'source' => 'existing brand asset',
            'copyright_owner' => 'Mwanafunzi Investor',
            'license' => 'Brand-owned internal asset',
            'internal_usage_notes' => 'DEMO fixture only; no external image was added.',
            'rights_verified' => true,
            'uploaded_by' => $demoAdmin->id,
        ]);
        $demoProject->gallery()->syncWithoutDetaching([$demoMedia->id => ['alt_text' => 'Existing Mwanafunzi Investor brand asset used as a local fixture.', 'caption' => 'DEMO fixture — not a project photograph.', 'sort_order' => 90]]);

        $demoCourse = Course::updateOrCreate(['slug' => 'demo-course-for-qa'], [
            'title' => '[DEMO] Course workflow fixture',
            'subtitle' => 'Local-only course record for enrollment and lesson checks.',
            'level' => 'Foundation',
            'duration' => 'DEMO / 1 session',
            'status' => 'draft',
            'is_featured' => false,
            'short_description' => 'DEMO DATA ONLY. Exercise course, module, lesson, FAQ and enrollment operations.',
            'full_description' => 'This fictional course is never presented as a live offering.',
            'learning_outcomes' => ['Verify enrollment persistence', 'Verify lesson access checks'],
            'prerequisites' => ['None — local QA only'],
            'instructor' => 'DEMO instructor',
            'cta_label' => 'Local QA only',
            'enrollment_available' => false,
            'sort_order' => 90,
        ]);
        $demoModule = CourseModule::updateOrCreate(['course_id' => $demoCourse->id, 'title' => '[DEMO] Module one'], ['description' => 'DEMO module for local testing.', 'sort_order' => 1]);
        CourseLesson::updateOrCreate(['course_module_id' => $demoModule->id, 'slug' => 'demo-lesson-one'], ['title' => '[DEMO] Lesson one', 'content' => '<p>DEMO DATA ONLY. This lesson is used by automated and manual local tests.</p>', 'sort_order' => 1, 'is_published' => true]);
        CourseFaq::updateOrCreate(['course_id' => $demoCourse->id, 'question' => '[DEMO] How is this fixture used?'], ['answer' => 'It exercises course FAQ and enrollment flows locally.', 'sort_order' => 1]);
        Enrollment::updateOrCreate(['user_id' => $demoClient->id, 'course_id' => $demoCourse->id], ['status' => EnrollmentStatus::Active, 'enrolled_at' => $now, 'metadata' => ['demo' => true]]);

        $category = ArticleCategory::updateOrCreate(['slug' => 'demo-editorial'], ['name' => '[DEMO] Editorial QA', 'description' => 'Local-only editorial fixture category.', 'is_active' => false, 'sort_order' => 90]);
        $tag = Tag::updateOrCreate(['slug' => 'demo-content'], ['name' => '[DEMO] Content QA', 'description' => 'Local-only tag.']);
        $author = TeamMember::where('is_active', true)->first();
        $article = Article::updateOrCreate(['slug' => 'demo-article-for-qa'], [
            'article_category_id' => $category->id,
            'team_member_id' => $author?->id,
            'title' => '[DEMO] Article workflow fixture',
            'excerpt' => 'DEMO DATA ONLY. A draft article for editorial testing.',
            'content' => '<p>DEMO DATA ONLY. This article is intentionally kept as a draft.</p>',
            'content_type' => 'article',
            'author' => 'DEMO editorial fixture',
            'status' => 'draft',
            'is_featured' => false,
            'reading_time' => 1,
        ]);
        $article->tags()->syncWithoutDetaching([$tag->id]);
        $comment = Comment::firstOrCreate(['article_id' => $article->id, 'email' => 'demo.reader@example.test', 'body' => 'DEMO comment for moderation testing.'], ['name' => 'DEMO Reader', 'status' => 'pending']);
        ArticleReaction::firstOrCreate(['article_id' => $article->id, 'user_id' => $demoClient->id, 'type' => 'like']);

        $demoProduct = Product::updateOrCreate(['slug' => 'demo-product-for-qa'], [
            'name' => '[DEMO] Product workflow fixture',
            'product_type' => 'Local test download',
            'availability' => 'draft',
            'short_description' => 'DEMO DATA ONLY. Exercise product availability and protected delivery flows.',
            'detailed_description' => 'This fictional product has no commercial availability.',
            'version' => 'DEMO 0.1',
            'price' => '0.00',
            'currency' => 'TZS',
            'download_format' => 'PDF',
            'system_requirements' => 'Local QA only.',
            'is_featured' => false,
            'sort_order' => 90,
        ]);
        ProductFeature::updateOrCreate(['product_id' => $demoProduct->id, 'title' => '[DEMO] Feature'], ['description' => 'Local-only feature fixture.', 'sort_order' => 1]);
        ProductVersion::updateOrCreate(['product_id' => $demoProduct->id, 'version' => 'DEMO 0.1'], ['notes' => 'Local-only fixture version.', 'released_at' => $now->toDateString()]);
        $order = Order::updateOrCreate(['order_number' => 'DEMO-ORDER-0001'], [
            'user_id' => $demoClient->id,
            'customer_email' => $demoClient->email,
            'customer_name' => $demoClient->name,
            'country' => 'TZ',
            'status' => OrderStatus::PendingPayment,
            'payment_status' => PaymentStatus::Failed,
            'payment_provider' => 'demo',
            'internal_payment_reference' => 'DEMO-PAYMENT-0001',
            'subtotal' => '0.00', 'discount' => '0.00', 'tax' => '0.00', 'total' => '0.00', 'currency' => 'TZS',
            'failure_reason' => 'DEMO DATA ONLY. No real payment was attempted.',
            'billing_details' => ['demo' => true],
            'terms_accepted_at' => $now,
        ]);
        OrderItem::updateOrCreate(['order_id' => $order->id, 'title' => $demoProduct->name], ['purchasable_type' => Product::class, 'purchasable_id' => $demoProduct->id, 'unit_price' => '0.00', 'quantity' => 1, 'line_total' => '0.00', 'currency' => 'TZS', 'snapshot' => ['demo' => true]]);
        Payment::updateOrCreate(['internal_reference' => 'DEMO-PAYMENT-0001'], ['order_id' => $order->id, 'provider' => 'demo', 'amount' => '0.00', 'currency' => 'TZS', 'status' => PaymentStatus::Failed, 'metadata' => ['demo' => true], 'failed_at' => $now]);

        ContactMessage::firstOrCreate(['email' => 'demo.enquirer@example.test', 'message' => 'DEMO enquiry for inbox and status workflow testing.'], ['name' => 'DEMO Enquirer', 'phone' => '+255700000002', 'business_unit_id' => $studio->id, 'category' => 'general', 'consented_at' => $now, 'status' => 'new']);
        NewsletterSubscriber::updateOrCreate(['email' => 'demo.subscriber@example.test'], ['user_id' => $demoClient->id, 'status' => NewsletterSubscriber::PENDING, 'source' => 'footer', 'consent_version' => 'DEMO', 'last_confirmation_sent_at' => $now]);
        StudioInquiry::updateOrCreate(['reference' => 'DEMO-STUDIO-0001'], ['user_id' => $demoClient->id, 'business_unit_id' => $studio->id, 'service_id' => $services->first()->id, 'name' => $demoClient->name, 'email' => $demoClient->email, 'phone' => $demoClient->phone, 'preferred_date' => $now->addDays(14)->toDateString(), 'preferred_time' => 'DEMO morning', 'location' => 'DEMO location', 'duration' => 'DEMO half day', 'budget_range' => 'DEMO quote required', 'description' => 'DEMO DATA ONLY. Studio enquiry used for status and portal workflow testing.', 'special_requirements' => 'Do not treat as a real booking.', 'status' => 'new']);

        $demoPage = Page::updateOrCreate(['key' => 'demo-audit-page'], ['slug' => 'demo-audit-page', 'name' => '[DEMO] Audit page fixture', 'page_type' => 'standard', 'status' => 'draft', 'is_visible' => false, 'hero_title' => '[DEMO] Audit page', 'hero_summary' => 'Local-only CMS fixture.', 'seo_title' => '[DEMO] Audit page', 'seo_description' => 'Local-only CMS fixture.']);
        PageSection::updateOrCreate(['page_id' => $demoPage->id, 'key' => 'demo-section'], ['section_type' => 'rich_text', 'heading' => '[DEMO] Section', 'body' => 'DEMO DATA ONLY. This section exercises CMS section editing.', 'sort_order' => 1, 'is_enabled' => true]);
        $demoPage->faqs()->updateOrCreate(['question' => '[DEMO] What is this page?'], ['answer' => 'A local-only CMS fixture for FAQ CRUD testing.', 'sort_order' => 1]);
    }
}
