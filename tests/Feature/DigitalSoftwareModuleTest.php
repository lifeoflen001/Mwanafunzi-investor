<?php

namespace Tests\Feature;

use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigitalSoftwareModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_digital_software_home_prioritises_projects_and_hospitality(): void
    {
        $this->get(route('digital-systems'))
            ->assertOk()
            ->assertSee('Hospitality &amp; Tourism', false)
            ->assertSee('View completed projects')
            ->assertSee('Capabilities')
            ->assertSee(route('digital-software.hospitality'));
    }

    public function test_published_digital_project_has_scoped_case_study_route(): void
    {
        $development = BusinessUnit::where('slug', 'development')->firstOrFail();
        $service = Service::create([
            'title' => 'Business systems', 'slug' => 'business-systems-case-study', 'business_unit_id' => $development->id,
            'short_description' => 'Systems shaped around real operations.', 'is_active' => true,
        ]);
        $project = Project::create([
            'title' => 'Hospitality Operations Desk', 'slug' => 'hospitality-operations-desk', 'industry' => 'Hospitality',
            'project_type' => 'Management system', 'project_status' => 'production_ready',
            'short_description' => 'A documented operations workflow.', 'full_introduction' => '<p>A factual overview.</p>',
            'status' => 'published', 'published_at' => now(), 'technologies' => ['Laravel', 'MySQL'],
        ]);
        $project->services()->attach($service);

        $this->get(route('digital-software.projects.show', $project))
            ->assertOk()
            ->assertSee('Hospitality Operations Desk')
            ->assertSee('Production Ready')
            ->assertSee('A factual overview.')
            ->assertSee('Laravel');
        $this->get(route('digital-software.projects', ['q' => 'Hospitality Operations']))
            ->assertOk()
            ->assertSee('Hospitality Operations Desk');
    }

    public function test_digital_project_route_rejects_other_modules_and_unpublished_records(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $studioService = Service::create([
            'title' => 'Photography', 'slug' => 'photography-digital-scope-test', 'business_unit_id' => $studio->id,
            'short_description' => 'Studio work.', 'is_active' => true,
        ]);
        $studioProject = Project::create(['title' => 'Studio record', 'slug' => 'studio-record-digital-scope-test', 'short_description' => 'Studio only.', 'status' => 'published', 'published_at' => now()]);
        $studioProject->services()->attach($studioService);
        $this->get(route('digital-software.projects.show', $studioProject))->assertNotFound();

        $development = BusinessUnit::where('slug', 'development')->firstOrFail();
        $service = Service::create(['title' => 'Software', 'slug' => 'software-digital-scope-test', 'business_unit_id' => $development->id, 'short_description' => 'Software.', 'is_active' => true]);
        $draft = Project::create(['title' => 'Unpublished system', 'slug' => 'unpublished-system-digital-scope-test', 'short_description' => 'Draft only.', 'status' => 'draft']);
        $draft->services()->attach($service);
        $this->get(route('digital-software.projects.show', $draft))->assertNotFound();
    }

    public function test_admin_can_manage_project_status_and_filter_catalogue(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->post(route('admin.projects.store'), [
            'title' => 'Tour Operator Workflow', 'slug' => 'tour-operator-workflow', 'industry' => 'Tourism',
            'project_type' => 'Management system', 'project_status' => 'active_development',
            'short_description' => 'A structured operational workflow.', 'status' => 'draft', 'display_order' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['slug' => 'tour-operator-workflow', 'project_status' => 'active_development']);
        $this->actingAs($admin)->get(route('admin.projects', ['status' => 'active_development']))
            ->assertOk()
            ->assertSee('Tour Operator Workflow');
    }

    public function test_verified_digital_software_records_are_published_and_hospitality_scoped(): void
    {
        $slugs = [
            'lodgix',
            'tour-operator-management-platform',
            'schoolix',
            'nideo-stockflow',
            'mwanafunzi-investor-platform',
        ];

        $this->assertSame(5, Project::whereIn('slug', $slugs)->count());

        $projects = Project::whereIn('slug', $slugs)
            ->with('services.businessUnit')
            ->get()
            ->keyBy('slug');

        foreach ($slugs as $slug) {
            $project = $projects->get($slug);

            $this->assertNotNull($project);
            $this->assertSame('published', $project->status);
            $this->assertSame('active_development', $project->project_status);
            $this->assertNull($project->featured_image);
            $this->assertTrue($project->services->contains(
                fn (Service $service): bool => $service->businessUnit?->slug === 'development'
            ));
        }

        $this->get(route('digital-software.hospitality'))
            ->assertOk()
            ->assertSee('Lodgix')
            ->assertSee('Tour Operator Management Platform')
            ->assertDontSee('A better digital front door and a clearer operation.')
            ->assertDontSee('From guest experience to back-office workflow.')
            ->assertSee('Tell us where the current workflow gets in the way.');
    }
}
