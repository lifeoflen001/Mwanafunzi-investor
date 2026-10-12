<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\EnrollmentStatus;
use App\Enums\EntitlementStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\CourseLessonCompletion;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\Entitlement;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
            ->assertSee('Getting started')
            ->assertSee('Explore learning')
            ->assertSee('Explore digital tools')
            ->assertSee('Start with the right course.')
            ->assertSee('Make the process visible.')
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

    public function test_returning_dashboard_uses_owned_progress_tools_orders_and_alerts_without_leaking(): void
    {
        $customer = User::factory()->create([
            'is_admin' => false,
            'email_verified_at' => now(),
            'phone' => '+255 700 000 001',
            'country' => 'TZ',
        ]);
        $otherCustomer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);

        $course = Course::create([
            'title' => 'Returning customer course',
            'slug' => 'returning-customer-course',
            'short_description' => 'A course owned by one customer only.',
            'full_description' => 'A private learning path for the dashboard test.',
            'status' => 'published',
            'currency' => 'TZS',
        ]);
        $module = CourseModule::create(['course_id' => $course->id, 'title' => 'First module']);
        $firstLesson = CourseLesson::create([
            'course_module_id' => $module->id,
            'title' => 'Completed lesson',
            'slug' => 'completed-lesson',
            'content' => '<p>Completed.</p>',
            'is_published' => true,
        ]);
        CourseLesson::create([
            'course_module_id' => $module->id,
            'title' => 'Next lesson',
            'slug' => 'next-lesson',
            'content' => '<p>Next.</p>',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $enrollment = Enrollment::create([
            'user_id' => $customer->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'enrolled_at' => now(),
        ]);
        CourseLessonCompletion::create([
            'user_id' => $customer->id,
            'enrollment_id' => $enrollment->id,
            'course_lesson_id' => $firstLesson->id,
            'completed_at' => now(),
        ]);

        $product = Product::create([
            'name' => 'Owned trading journal',
            'slug' => 'owned-trading-journal',
            'product_type' => 'template',
            'short_description' => 'A private owned tool.',
            'availability' => 'available',
            'price' => 25000,
            'currency' => 'TZS',
        ]);
        $order = Order::create([
            'order_number' => 'ORD-RETURNING-001',
            'user_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'status' => OrderStatus::Paid,
            'payment_status' => PaymentStatus::Paid,
            'subtotal' => 25000,
            'total' => 25000,
            'currency' => 'TZS',
        ]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'purchasable_type' => Product::class,
            'purchasable_id' => $product->id,
            'title' => $product->name,
            'unit_price' => 25000,
            'quantity' => 1,
            'line_total' => 25000,
            'currency' => 'TZS',
        ]);
        Entitlement::create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'product_id' => $product->id,
            'customer_email' => $customer->email,
            'type' => 'product',
            'status' => EntitlementStatus::Active,
        ]);

        $restrictedProduct = Product::create([
            'name' => 'Expired market workbook',
            'slug' => 'expired-market-workbook',
            'product_type' => 'workbook',
            'short_description' => 'An expired tool.',
            'availability' => 'available',
        ]);
        Entitlement::create([
            'user_id' => $customer->id,
            'product_id' => $restrictedProduct->id,
            'customer_email' => $customer->email,
            'type' => 'product',
            'status' => EntitlementStatus::Expired,
            'ends_at' => now()->subDay(),
        ]);

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Continue learning')
            ->assertSee('Returning customer course')
            ->assertSee('50%')
            ->assertSee('Owned trading journal')
            ->assertSee('ORD-RETURNING-001')
            ->assertSee('Access needs attention')
            ->assertSee('Expired market workbook');

        $this->actingAs($otherCustomer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertDontSee('ORD-RETURNING-001')
            ->assertDontSee('Access needs attention');
    }

    public function test_pending_payment_is_returning_state_with_a_specific_actionable_alert(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);
        $order = Order::create([
            'order_number' => 'ORD-PENDING-001',
            'user_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'status' => OrderStatus::PendingPayment,
            'payment_status' => PaymentStatus::Pending,
            'currency' => 'TZS',
        ]);

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Payment still processing')
            ->assertSee('ORD-PENDING-001')
            ->assertSee('View order')
            ->assertDontSee('Getting started');
    }
}
