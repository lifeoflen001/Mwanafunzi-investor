<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_customer_workspace_routes_render_for_an_authenticated_customer(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);

        foreach (['account.dashboard', 'account.courses', 'account.downloads', 'account.orders', 'account.profile', 'account.security'] as $route) {
            $this->actingAs($customer)->get(route($route))->assertOk();
        }
    }

    public function test_customer_can_update_profile_but_email_is_not_a_profile_field(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now(), 'email' => 'customer@example.com']);

        $this->actingAs($customer)->put(route('account.profile.update'), [
            'name' => 'Updated Customer',
            'phone' => '+255 787 172 686',
            'country' => 'tz',
            'email' => 'changed@example.com',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'Updated Customer', 'email' => 'customer@example.com', 'country' => 'TZ']);
    }

    public function test_customer_password_requires_current_password(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now(), 'password' => 'old-password-123']);

        $this->actingAs($customer)->put(route('account.security.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_customer_avatar_is_validated_and_stored_on_public_disk(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);

        $this->actingAs($customer)->post(route('account.profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('profile.jpg', 500, 500),
        ])->assertSessionHas('success');

        $path = $customer->fresh()->avatar_path;
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }
}
