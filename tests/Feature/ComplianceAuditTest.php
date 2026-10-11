<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplianceAuditTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_compliance_panel_is_admin_only_and_reports_review_state(): void
    {
        $this->get(route('admin.compliance'))->assertRedirect(route('admin.login'));

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.compliance'))
            ->assertOk()
            ->assertSee('Compliance &amp; privacy', false)
            ->assertSee('Business information incomplete')
            ->assertSee('Cookie &amp; tracking inventory', false)
            ->assertSee('Legal review required');
    }

    public function test_published_testimonials_require_confirmed_permission(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.testimonials.store'), [
            'client_name' => 'Unconfirmed client',
            'testimonial' => 'A supplied perspective that has not been cleared for publication.',
            'is_published' => 1,
            'sort_order' => 1,
        ])->assertSessionHasErrors('consent_confirmed');

        $this->assertDatabaseMissing('testimonials', ['client_name' => 'Unconfirmed client']);

        $this->actingAs($admin)->post(route('admin.testimonials.store'), [
            'client_name' => 'Confirmed client',
            'testimonial' => 'A supplied perspective cleared for publication.',
            'consent_confirmed' => 1,
            'consent_notes' => 'Permission recorded by the admin desk.',
            'is_published' => 1,
            'sort_order' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('testimonials', ['client_name' => 'Confirmed client', 'is_published' => 1, 'consent_confirmed' => 1]);
        $this->assertNotNull(Testimonial::where('client_name', 'Confirmed client')->firstOrFail()->consent_confirmed_at);
    }
}
