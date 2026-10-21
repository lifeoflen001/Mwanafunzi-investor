<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Media;
use App\Models\BusinessUnit;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_services_and_projects_are_admin_managed_and_publicly_visible(): void
    {
        $this->get(route('services'))->assertOk()->assertSee(route('forex-academy'))->assertSee(route('digital-systems'))->assertSee(route('creative-studio'));
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.services.store'), [
            'title' => 'Custom Systems', 'slug' => 'custom-systems', 'short_description' => 'Systems shaped around the real workflow.',
            'detailed_description' => '<p>Structured delivery copy.</p><script>alert(1)</script>', 'pricing_type' => 'starting_from', 'starting_price' => 1500000,
            'currency' => 'TZS', 'included_features' => "Discovery\nWorkflow mapping", 'sort_order' => 1, 'is_active' => 1, 'is_featured' => 1,
        ])->assertRedirect();

        $service = Service::where('slug', 'custom-systems')->firstOrFail();
        $this->assertSame(['Discovery', 'Workflow mapping'], $service->included_features);
        $this->assertSame('<p>Structured delivery copy.</p>', $service->detailed_description);
        $this->get(route('services.show', $service))->assertOk()->assertSee('Starting from TZS 1,500,000')->assertSee('Workflow mapping');

        $this->actingAs($admin)->post(route('admin.projects.store'), [
            'title' => 'Operations Platform', 'slug' => 'operations-platform', 'project_type' => 'Operational system', 'industry' => 'Operations',
            'short_description' => 'A structured system for a real workflow.', 'status' => 'published', 'display_order' => 1, 'is_featured' => 1,
            'service_ids' => [$service->id], 'technologies' => "Laravel\nMySQL", 'features' => 'Workflow visibility | One place for the operating record.',
            'results' => 'Operations visibility | Centralized | Fragmented information brought into one workspace.',
        ])->assertRedirect();

        $project = Project::where('slug', 'operations-platform')->firstOrFail();
        $this->assertDatabaseHas('project_service', ['project_id' => $project->id, 'service_id' => $service->id]);
        $this->get(route('projects'))->assertOk()->assertSee('Operations Platform');
        $this->get(route('projects.show', $project))->assertOk()->assertSee('Centralized')->assertSee('Laravel')->assertSee('Workflow visibility');
    }

    public function test_unpublished_projects_and_unapproved_testimonials_stay_private(): void
    {
        $project = Project::create(['title' => 'Private case study', 'slug' => 'private-case-study', 'short_description' => 'Not ready for publication.', 'status' => 'draft']);
        $this->get(route('projects.show', $project))->assertNotFound();

        Testimonial::create(['client_name' => 'Private client', 'testimonial' => 'Not approved.', 'is_published' => false, 'is_featured' => true, 'sort_order' => 1]);
        $this->get(route('home'))->assertOk()->assertDontSee('Not approved.');

        Testimonial::create(['client_name' => 'Approved client', 'testimonial' => 'A supplied and approved perspective.', 'is_published' => true, 'is_featured' => true, 'sort_order' => 1]);
        $this->get(route('home'))->assertOk()->assertSee('A supplied and approved perspective.');
    }

    public function test_public_testimonial_sections_hide_empty_states_and_use_editorial_cards(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('testimonial-card', false)
            ->assertDontSee('Client perspectives are being prepared.');

        $this->get(route('creative-studio.testimonials'))
            ->assertOk()
            ->assertDontSee('service-testimonial-placeholder', false);

        Testimonial::create([
            'client_name' => 'Confirmed client',
            'client_role' => 'Project lead',
            'company' => 'Example organisation',
            'testimonial' => 'A considered perspective from a completed project.',
            'is_published' => true,
            'is_featured' => true,
            'consent_confirmed' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('testimonial-card', false)
            ->assertSee('testimonial-section-heading', false)
            ->assertSee('A considered perspective from a completed project.')
            ->assertSee('Confirmed client');
    }

    public function test_creative_studio_project_uses_editorial_gallery_without_repeating_featured_image(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $studioService = Service::create(['title' => 'Studio coverage', 'slug' => 'studio-coverage-test', 'business_unit_id' => $studio->id, 'short_description' => 'Confirmed studio coverage.', 'pricing_type' => 'custom_quote', 'is_active' => true]);
        $featured = Media::create(['disk' => 'public', 'path' => 'test/featured.webp', 'filename' => 'featured.webp', 'mime_type' => 'image/webp', 'width' => 1600, 'height' => 1067, 'alt_text' => 'A confirmed featured frame']);
        $gallery = Media::create(['disk' => 'public', 'path' => 'test/gallery.webp', 'filename' => 'gallery.webp', 'mime_type' => 'image/webp', 'width' => 800, 'height' => 1200, 'alt_text' => 'A confirmed gallery frame']);
        $project = Project::create([
            'title' => 'Studio collection', 'slug' => 'studio-collection', 'project_type' => 'Event photography',
            'creative_category' => 'Event photography', 'media_type' => 'photography', 'short_description' => 'A confirmed studio collection.',
            'status' => 'published', 'published_at' => now(), 'featured_image' => $featured->path, 'photography_type' => 'Selected work',
        ]);
        $project->services()->attach($studioService);
        $project->gallery()->attach($featured->id, ['sort_order' => 0]);
        $project->gallery()->attach($gallery->id, ['sort_order' => 1, 'alt_text' => 'A confirmed gallery frame']);

        $response = $this->get(route('projects.show', $project));
        $response->assertOk()->assertSee('A confirmed gallery frame')->assertSee('data-gallery-viewer')->assertSee('Enquire about event photography');
        $response->assertSee('1 additional frame.')->assertDontSee('Related work.')->assertSee('Back to creative projects');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->put(route('admin.projects.update', $project), [
            'title' => 'Studio collection edited', 'slug' => 'studio-collection-edited', 'project_type' => 'Event photography',
            'creative_category' => 'Event photography', 'media_type' => 'photography', 'photography_type' => 'Selected work',
            'services_provided' => "Event photography\nPortraits", 'short_description' => 'An edited confirmed studio collection.',
            'featured_image' => $featured->path, 'featured_focal_point' => 'center top', 'status' => 'published', 'published_at' => now()->toDateTimeString(),
            'display_order' => 2, 'service_ids' => [$studioService->id], 'gallery' => [
                ['media_id' => $gallery->id, 'alt_text' => 'A confirmed gallery frame', 'caption' => 'Edited order', 'sort_order' => 0],
                ['media_id' => $featured->id, 'alt_text' => 'A confirmed featured frame', 'caption' => '', 'sort_order' => 1],
            ],
        ])->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'slug' => 'studio-collection-edited', 'featured_focal_point' => 'center top']);
        $this->assertDatabaseHas('project_media', ['project_id' => $project->id, 'media_id' => $gallery->id, 'sort_order' => 0, 'caption' => 'Edited order']);
    }
}
