<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\Course;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\User;
use App\Support\PublicSiteData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CurrentAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_studio_service_details_are_scoped_and_bind_by_slug(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $development = BusinessUnit::where('slug', 'development')->firstOrFail();
        $studioService = Service::create(['business_unit_id' => $studio->id, 'title' => 'Audit studio service', 'slug' => 'audit-studio-service', 'short_description' => 'A service used for route boundary coverage.', 'pricing_type' => 'custom_quote', 'pricing_label' => 'Custom quote', 'is_active' => true, 'sort_order' => 98]);
        $otherModuleService = Service::create(['business_unit_id' => $development->id, 'title' => 'Audit development service', 'slug' => 'audit-development-service', 'short_description' => 'A service used for route boundary coverage.', 'pricing_type' => 'custom_quote', 'pricing_label' => 'Custom quote', 'is_active' => true, 'sort_order' => 99]);

        $this->get(route('creative-studio.services.show', $studioService))->assertOk()->assertSee($studioService->title);
        $this->get(route('creative-studio.services.show', $otherModuleService))->assertNotFound();

        $studioService->update(['is_active' => false]);
        $this->get(route('creative-studio.services.show', $studioService))->assertNotFound();
        $this->get('/creative-studio/services/no-such-studio-service')->assertNotFound();
    }

    public function test_active_studio_services_are_published_on_the_public_collection(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        Service::create(['business_unit_id' => $studio->id, 'title' => 'Brand photography', 'slug' => 'brand-photography', 'short_description' => 'Brand imagery.', 'pricing_type' => 'custom_quote', 'pricing_label' => 'Custom quote', 'is_active' => true, 'sort_order' => 1]);
        Service::create(['business_unit_id' => $studio->id, 'title' => 'Videography and editing', 'slug' => 'videography-and-editing', 'short_description' => 'Video work.', 'pricing_type' => 'custom_quote', 'pricing_label' => 'Custom quote', 'is_active' => true, 'sort_order' => 2]);
        PublicSiteData::forgetCache();

        $this->get(route('creative-studio.services'))->assertOk()->assertSee('Brand photography')->assertSee('Videography and editing');
    }

    public function test_blank_blog_social_rows_are_ignored_and_partial_rows_are_validated(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = ArticleCategory::firstOrFail();
        $payload = ['article_category_id' => $category->id, 'title' => 'Optional social links', 'content' => 'A useful editorial note.', 'status' => 'draft', 'social_links' => [['platform' => '', 'url' => '', 'label' => '']]];

        $this->actingAs($admin)->post(route('admin.articles.store'), $payload)->assertRedirect();
        $article = Article::where('slug', 'optional-social-links')->firstOrFail();
        $this->assertDatabaseCount('article_social_links', 0);

        $this->actingAs($admin)->post(route('admin.articles.store'), array_merge($payload, ['title' => 'Partial platform row', 'social_links' => [['platform' => 'instagram', 'url' => '', 'label' => '']]]))->assertSessionHasErrors('social_links.0.url');
        $this->actingAs($admin)->post(route('admin.articles.store'), array_merge($payload, ['title' => 'Partial URL row', 'social_links' => [['platform' => '', 'url' => 'https://instagram.com/example', 'label' => '']]]))->assertSessionHasErrors('social_links.0.platform');
        $this->get(route('admin.articles.edit', $article))->assertOk()->assertSee('Choose a platform');
    }

    public function test_newsletter_pending_confirmation_is_not_presented_as_active(): void
    {
        Mail::fake();
        $this->post(route('newsletter.subscribe'), ['email' => 'audit-pending@example.test'])->assertRedirect();
        $this->get(route('home'))->assertSee('pending confirmation')->assertSee('Check your inbox to confirm your email.');
    }

    public function test_production_errors_render_safe_page_without_exception_details(): void
    {
        Config::set('app.debug', false);
        $this->view('errors.500', [])->assertSee('Something needs another look.')->assertDontSee('Exception trace')->assertDontSee('sensitive query details');
        $this->assertFalse(config('app.debug'));
    }

    public function test_public_faq_route_and_legacy_alias_render_published_answers(): void
    {
        $course = Course::whereIn('status', ['published', 'open', 'coming_soon'])->firstOrFail();
        $course->faqs()->create([
            'question' => 'How does this published FAQ work?',
            'answer' => 'It is displayed from the course content that is already public.',
            'sort_order' => 1,
        ]);

        $this->get(route('faqs'))
            ->assertOk()
            ->assertSee('Frequently asked questions.')
            ->assertSee('How does this published FAQ work?')
            ->assertSee('FAQPage');

        $this->get('/faq')->assertRedirect(route('faqs'));
    }

    public function test_error_pages_use_brand_surfaces_instead_of_red_alert_containers(): void
    {
        foreach (['403', '404', '419', '422', '429', '500', '503'] as $status) {
            $this->view("errors.{$status}", [])
                ->assertSee('--error-night:#111211')
                ->assertSee('--error-paper:#ebe6dc')
                ->assertSee('--error-copper')
                ->assertDontSee('error-message')
                ->assertDontSee('The page you are looking for')
                ->assertDontSee('#fff1ef')
                ->assertDontSee('#943d33');
        }
    }

    public function test_admin_can_update_error_page_copy_and_public_template_uses_it(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Error pages')
            ->assertSee('error_pages[404][heading]', false);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'brand_name' => 'Mwanafunzi Investor',
            'contact_email' => 'desk@example.test',
            'default_seo_title' => 'Mwanafunzi Investor',
            'default_seo_description' => 'Student of Money. Systems. Discipline.',
            'risk_disclaimer' => 'Educational content only.',
            'error_pages' => [
                '404' => [
                    'title' => 'Find your way back',
                    'eyebrow' => '404 / Keep moving',
                    'heading' => 'The page is not here, but the process continues.',
                    'action_label' => 'Go to Mwanafunzi home',
                    'footer_copy' => 'Updated error footer copy.',
                    'footer_statement' => 'Keep learning.',
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('site_settings', ['key' => 'error_pages', 'type' => 'json']);
        $this->view('errors.404', [])
            ->assertSee('Find your way back')
            ->assertSee('The page is not here, but the process continues.')
            ->assertSee('Go to Mwanafunzi home')
            ->assertSee('Updated error footer copy.')
            ->assertSee('Keep learning.');
    }

    public function test_course_without_curriculum_explains_that_enrollment_is_not_open(): void
    {
        $course = Course::where('slug', 'forex-foundations')->firstOrFail();
        $course->update(['enrollment_available' => false]);

        $this->get(route('courses.show', $course))->assertOk()->assertSee('No modules are published yet, so enrollment is not open.');
    }

    public function test_unread_enquiry_dashboard_link_filters_by_read_state_not_only_new_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        ContactMessage::create(['name' => 'In progress enquiry', 'email' => 'in-progress@example.test', 'category' => 'general', 'message' => 'This needs a reply.', 'status' => 'in_progress']);
        ContactMessage::create(['name' => 'Already read enquiry', 'email' => 'read@example.test', 'category' => 'general', 'message' => 'Already reviewed.', 'status' => 'in_progress', 'read_at' => now()]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.messages', ['unread' => 1]), false)
            ->assertDontSee(route('admin.messages', ['status' => 'new']), false);

        $this->actingAs($admin)->get(route('admin.messages', ['unread' => 1]))
            ->assertOk()
            ->assertSee('in-progress@example.test')
            ->assertDontSee('read@example.test');
    }
}
