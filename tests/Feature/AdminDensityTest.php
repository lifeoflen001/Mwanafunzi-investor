<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDensityTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_shared_admin_table_pages_keep_the_compact_primitives(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'active']);

        $this->actingAs($admin)
            ->get(route('admin.testimonials'))
            ->assertOk()
            ->assertSee('admin-table-card')
            ->assertSee('admin-table-head')
            ->assertSee('admin-empty-state');

        $this->actingAs($admin)
            ->get(route('admin.seo'))
            ->assertOk()
            ->assertSee('admin-seo-table-card')
            ->assertSee('admin-table-head');

        $this->actingAs($admin)
            ->get(route('admin.compliance'))
            ->assertOk()
            ->assertSee('admin-table-head')
            ->assertSee('admin-content-grid-wide');
    }

    public function test_navigation_editor_retains_editable_visibility_and_compact_form_hooks(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'active']);

        $this->actingAs($admin)
            ->get(route('admin.navigation'))
            ->assertOk()
            ->assertSee('admin-grid-two')
            ->assertSee('admin-navigation-page')
            ->assertSee('admin-navigation-row')
            ->assertSee('admin-inline-form')
            ->assertSee('name="is_visible"', false);
    }
}
