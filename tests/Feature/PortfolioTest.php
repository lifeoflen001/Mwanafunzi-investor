<?php

namespace Tests\Feature;

use App\Models\Project;
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
}
