<?php

namespace Tests\Feature;

use App\Models\BusinessUnit;
use App\Models\Service;
use App\Models\StudioInquiry;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudioInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_studio_booking_enquiry_is_persisted_and_visible_to_owner_and_admin(): void
    {
        Storage::fake('local');
        $client = User::factory()->create(['status' => 'active']);
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'active']);
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $service = Service::create([
            'title' => 'Portrait photography', 'slug' => 'portrait-photography-test', 'business_unit_id' => $studio->id,
            'short_description' => 'Portraits made with care.', 'pricing_type' => 'custom_quote', 'currency' => 'TZS',
            'is_active' => true, 'sort_order' => 1,
        ]);

        $this->actingAs($client)->get(route('creative-studio.book'))->assertOk()->assertSee('Start with the brief.');
        $this->actingAs($client)->post(route('creative-studio.book.store'), [
            'name' => $client->name,
            'email' => $client->email,
            'phone' => '+255700000000',
            'service_id' => $service->id,
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => 'Morning',
            'location' => 'Arusha',
            'duration' => 'Half day',
            'description' => 'A real portrait brief with enough detail for the studio team to review.',
            'special_requirements' => 'Please keep the gallery private.',
            'reference_files' => [UploadedFile::fake()->create('brief.pdf', 100, 'application/pdf')],
            'consent' => 1,
        ])->assertRedirect(route('creative-studio.book'));

        $inquiry = StudioInquiry::firstOrFail();
        $this->assertSame('new', $inquiry->status);
        $this->assertSame($client->id, $inquiry->user_id);
        Storage::disk('local')->assertExists($inquiry->reference_files[0]['path']);

        $this->actingAs($client)->get(route('account.studio.inquiries'))->assertOk()->assertSee($inquiry->reference);
        $this->actingAs($client)->get(route('account.studio.inquiries.show', $inquiry))->assertOk()->assertSee('This is an enquiry, not a confirmed booking.');
        $otherClient = User::factory()->create(['status' => 'active']);
        $this->actingAs($otherClient)->get(route('account.studio.inquiries.show', $inquiry))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.studio.inquiries'))->assertOk()->assertSee($inquiry->reference);
        $this->actingAs($admin)->get(route('admin.studio.inquiries.show', $inquiry))->assertOk()->assertSee('Private reference files');
        $this->actingAs($admin)->patch(route('admin.studio.inquiries.update', $inquiry), ['status' => 'reviewing', 'admin_notes' => 'Initial review completed.'])->assertRedirect();
        $this->assertDatabaseHas('studio_inquiries', ['id' => $inquiry->id, 'status' => 'reviewing', 'admin_notes' => 'Initial review completed.']);
    }

    public function test_project_context_is_retained_on_studio_enquiry(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();
        $service = Service::create(['title' => 'Studio context service', 'slug' => 'studio-context-service', 'business_unit_id' => $studio->id, 'short_description' => 'Confirmed studio coverage.', 'pricing_type' => 'custom_quote', 'is_active' => true]);
        $project = Project::create(['title' => 'Confirmed studio work', 'slug' => 'confirmed-studio-work', 'short_description' => 'A confirmed collection.', 'status' => 'published', 'published_at' => now()]);
        $project->services()->attach($service);

        $this->post(route('creative-studio.book.store'), [
            'name' => 'Test visitor', 'email' => 'studio-context@example.test', 'service_id' => $service->id, 'project_id' => $project->id,
            'description' => 'A sufficiently detailed studio brief for a project-specific enquiry.', 'consent' => 1,
        ])->assertRedirect(route('creative-studio.book'));

        $this->assertDatabaseHas('studio_inquiries', ['project_id' => $project->id, 'service_id' => $service->id, 'email' => 'studio-context@example.test']);
    }

    public function test_reference_files_have_a_combined_request_size_limit(): void
    {
        $this->from(route('creative-studio.book'))
            ->post(route('creative-studio.book.store'), [
                'name' => 'Upload test',
                'email' => 'upload-limit@example.test',
                'description' => 'A sufficiently detailed studio brief for testing the reference file size limit.',
                'consent' => 1,
                'reference_files' => [
                    UploadedFile::fake()->create('one.pdf', 11264, 'application/pdf'),
                    UploadedFile::fake()->create('two.pdf', 11264, 'application/pdf'),
                    UploadedFile::fake()->create('three.pdf', 11264, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('creative-studio.book'))
            ->assertSessionHasErrors('reference_files');
    }
}
