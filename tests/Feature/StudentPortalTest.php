<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\Enrollment;
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

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Start with one deliberate action.')
            ->assertSee('Learning overview')
            ->assertSee('Digital tools')
            ->assertSee('Recent purchases')
            ->assertSee('Recommended next action')
            ->assertDontSee('portal-sidebar-footer');

        foreach (['account.courses', 'account.downloads', 'account.orders', 'account.profile', 'account.security'] as $route) {
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

    public function test_customer_can_open_lessons_and_track_real_course_progress(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);
        $otherCustomer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);
        $course = Course::create([
            'title' => 'Market structure foundations',
            'slug' => 'market-structure-foundations-test',
            'short_description' => 'A practical course for deliberate learners.',
            'full_description' => 'Build a repeatable foundation.',
            'status' => 'published',
        ]);
        $module = CourseModule::create(['course_id' => $course->id, 'title' => 'The foundation']);
        $firstLesson = CourseLesson::create([
            'course_module_id' => $module->id,
            'title' => 'Read the market',
            'slug' => 'read-the-market',
            'content' => '<p>Understand the context before acting.</p>',
            'is_published' => true,
        ]);
        $secondLesson = CourseLesson::create([
            'course_module_id' => $module->id,
            'title' => 'Build the rule',
            'slug' => 'build-the-rule',
            'content' => '<p>Turn the idea into a rule.</p>',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $enrollment = Enrollment::create([
            'user_id' => $customer->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Continue learning')
            ->assertSee('In progress')
            ->assertSee('Completed');

        $this->actingAs($customer)
            ->get(route('account.courses.show', $enrollment))
            ->assertOk()
            ->assertSee('0%')
            ->assertSee('Read the market');

        $this->actingAs($customer)
            ->get(route('account.courses.lessons.show', [$enrollment, $firstLesson]))
            ->assertOk()
            ->assertSee('Understand the context before acting.');

        $this->actingAs($customer)
            ->post(route('account.courses.lessons.complete', [$enrollment, $firstLesson]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_lesson_completions', [
            'user_id' => $customer->id,
            'enrollment_id' => $enrollment->id,
            'course_lesson_id' => $firstLesson->id,
        ]);
        $this->assertSame(50, $enrollment->fresh()->load(['course.modules.lessons', 'lessonCompletions'])->progressPercent());

        $this->actingAs($customer)
            ->post(route('account.courses.lessons.complete', [$enrollment, $firstLesson]))
            ->assertSessionHas('success');
        $this->assertDatabaseCount('course_lesson_completions', 1);

        $this->actingAs($customer)
            ->post(route('account.courses.lessons.complete', [$enrollment, $secondLesson]))
            ->assertSessionHas('success');
        $this->assertSame(EnrollmentStatus::Completed, $enrollment->fresh()->status);

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Completed')
            ->assertSee('1');

        $this->actingAs($otherCustomer)
            ->get(route('account.courses.lessons.show', [$enrollment, $firstLesson]))
            ->assertForbidden();
    }
}
