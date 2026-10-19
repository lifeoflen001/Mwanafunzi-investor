<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\Course;
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

    public function test_course_without_curriculum_explains_that_enrollment_is_not_open(): void
    {
        $course = Course::where('slug', 'forex-foundations')->firstOrFail();
        $course->update(['enrollment_available' => false]);

        $this->get(route('courses.show', $course))->assertOk()->assertSee('No modules are published yet, so enrollment is not open.');
    }
}
