<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\EntitlementStatus;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAsset;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use App\Services\RefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CommerceTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_checkout_uses_server_price_and_preserves_snapshot(): void
    {
        $user = $this->customer();
        $product = $this->product(['price' => '25000.00', 'availability' => 'available']);
        $response = $this->actingAs($user)->post(route('checkout.product.store', $product), ['name' => $user->name, 'email' => $user->email, 'phone' => '', 'country' => 'TZ', 'terms' => '1', 'price' => '1.00']);
        $response->assertRedirect();
        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('25000.00', (string) $order->total);
        $this->assertSame('25000.00', (string) $order->items->first()->unit_price);
        $product->update(['price' => '30000.00']);
        $this->assertSame('25000.00', (string) $order->items->first()->fresh()->unit_price);
    }

    public function test_unavailable_and_coming_soon_products_cannot_checkout(): void
    {
        $user = $this->customer();
        foreach (['unavailable', 'coming_soon'] as $availability) {
            $product = $this->product(['availability' => $availability, 'price' => '100.00']);
            $this->actingAs($user)->get(route('checkout.product', $product))->assertNotFound();
        }
    }

    public function test_free_product_creates_paid_zero_order_without_gateway_charge(): void
    {
        $user = $this->customer();
        $product = $this->product(['availability' => 'available', 'price' => null]);
        $this->actingAs($user)->post(route('checkout.product.store', $product), ['name' => $user->name, 'email' => $user->email, 'terms' => '1'])->assertRedirect();
        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('0.00', (string) $order->total);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => PaymentStatus::Paid->value]);
        $this->assertDatabaseHas('entitlements', ['order_id' => $order->id, 'product_id' => $product->id, 'status' => EntitlementStatus::Active->value]);
    }

    public function test_sandbox_payment_is_idempotent_and_revenue_is_not_double_counted(): void
    {
        $user = $this->customer();
        $product = $this->product(['availability' => 'available', 'price' => '25000.00']);
        $order = app(CheckoutService::class)->createForProduct($user, $product, ['name' => $user->name, 'email' => $user->email, 'country' => 'TZ']);
        app(CheckoutService::class)->initialize($order, route('payments.flutterwave.return'));
        $payment = $order->payments()->firstOrFail();
        $verified = ['status' => 'successful', 'id' => 'sandbox-transaction-1', 'tx_ref' => $payment->internal_reference, 'amount' => '25000.00', 'currency' => 'TZS'];
        app(PaymentService::class)->verifyAndComplete($payment, $verified, 'sandbox-event-1');
        app(PaymentService::class)->verifyAndComplete($payment, $verified, 'sandbox-event-1');
        $this->assertSame(1, $order->fresh()->entitlements()->count());
        $this->assertSame(1, $order->payments()->where('status', PaymentStatus::Paid->value)->count());
        $this->assertSame(25000, (int) Order::where('payment_status', PaymentStatus::Paid->value)->sum('total'));
    }

    public function test_wrong_amount_currency_and_reference_are_rejected(): void
    {
        $user = $this->customer();
        $product = $this->product(['availability' => 'available', 'price' => '25000.00']);
        $order = app(CheckoutService::class)->createForProduct($user, $product, ['name' => $user->name, 'email' => $user->email]);
        app(CheckoutService::class)->initialize($order, route('payments.flutterwave.return'));
        $payment = $order->payments()->firstOrFail();
        foreach ([['amount' => '1.00', 'currency' => 'TZS', 'tx_ref' => $payment->internal_reference], ['amount' => '25000.00', 'currency' => 'USD', 'tx_ref' => $payment->internal_reference], ['amount' => '25000.00', 'currency' => 'TZS', 'tx_ref' => 'wrong']] as $data) {
            try { app(PaymentService::class)->verifyAndComplete($payment, ['status' => 'successful', 'id' => uniqid(), ...$data]); $this->fail('Invalid payment data was accepted.'); } catch (\RuntimeException $exception) { $this->assertNotSame('', $exception->getMessage()); }
        }
        $this->assertTrue(true);
    }

    public function test_download_requires_entitlement_and_private_storage(): void
    {
        Storage::fake('local');
        $owner = $this->customer(); $other = $this->customer(['email' => 'other@example.com']);
        $product = $this->product(['availability' => 'available', 'price' => null]);
        Storage::disk('local')->put('commerce/'.$product->id.'/guide.pdf', 'private content');
        $asset = ProductAsset::create(['product_id' => $product->id, 'name' => 'guide.pdf', 'path' => 'commerce/'.$product->id.'/guide.pdf', 'disk' => 'local', 'mime_type' => 'application/pdf', 'extension' => 'pdf', 'size' => 15, 'download_policy' => 'current_and_future']);
        $order = app(CheckoutService::class)->createForProduct($owner, $product, ['name' => $owner->name, 'email' => $owner->email]);
        app(CheckoutService::class)->initialize($order, route('payments.flutterwave.return'));
        $signed = URL::temporarySignedRoute('downloads.asset', now()->addMinutes(5), ['asset' => $asset->id, 'token' => 'test']);
        $this->actingAs($other)->get($signed)->assertNotFound();
        $this->actingAs($owner)->get($signed)->assertOk();
        $entitlement = $owner->entitlements()->firstOrFail();
        $this->assertDatabaseHas('download_logs', ['entitlement_id' => $entitlement->id, 'product_asset_id' => $asset->id]);
    }

    public function test_course_free_paid_and_waitlist_flows_are_separate(): void
    {
        $user = $this->customer();
        $free = $this->course(['slug' => 'free-course', 'status' => 'published', 'enrollment_available' => true, 'price' => null]);
        $this->actingAs($user)->post(route('checkout.course.store', $free), ['name' => $user->name, 'email' => $user->email, 'terms' => '1'])->assertRedirect();
        $this->assertDatabaseHas('enrollments', ['user_id' => $user->id, 'course_id' => $free->id, 'status' => EnrollmentStatus::Active->value]);
        $paid = $this->course(['slug' => 'paid-course', 'status' => 'published', 'enrollment_available' => true, 'price' => '50000.00']);
        $this->actingAs($user)->post(route('checkout.course.store', $paid), ['name' => $user->name, 'email' => $user->email, 'terms' => '1'])->assertRedirect();
        $paidOrder = Order::whereHas('items', fn ($q) => $q->where('purchasable_id', $paid->id))->latest()->firstOrFail();
        $payment = $paidOrder->payments()->firstOrFail();
        $this->actingAs($user)->post(route('payments.sandbox.complete', $payment), ['result' => 'successful'])->assertRedirect(route('payments.success', $paidOrder));
        $this->assertDatabaseHas('enrollments', ['user_id' => $user->id, 'course_id' => $paid->id, 'status' => EnrollmentStatus::Active->value]);
        $soon = $this->course(['slug' => 'soon-course', 'status' => 'coming_soon', 'enrollment_available' => false]);
        $this->post(route('courses.waitlist', $soon), ['name' => 'Waitlist Student', 'email' => 'waitlist@example.com', 'consent' => '1'])->assertRedirect();
        $this->post(route('courses.waitlist', $soon), ['name' => 'Waitlist Student', 'email' => 'waitlist@example.com', 'consent' => '1'])->assertRedirect();
        $this->assertDatabaseCount('course_waitlists', 1);
    }

    public function test_refund_revokes_access_and_customer_idor_is_denied(): void
    {
        $owner = $this->customer(); $other = $this->customer(['email' => 'second@example.com']);
        $product = $this->product(['availability' => 'available', 'price' => '10000.00']);
        $order = app(CheckoutService::class)->createForProduct($owner, $product, ['name' => $owner->name, 'email' => $owner->email]);
        app(CheckoutService::class)->initialize($order, route('payments.flutterwave.return'));
        $payment = $order->payments()->firstOrFail();
        app(PaymentService::class)->verifyAndComplete($payment, ['status' => 'successful', 'id' => 'refund-test', 'tx_ref' => $payment->internal_reference, 'amount' => '10000.00', 'currency' => 'TZS'], 'refund-event');
        $this->actingAs($other)->get(route('account.orders.show', $order))->assertForbidden();
        app(RefundService::class)->markRefunded($order->fresh(), $owner, '10000.00', 'Customer refund test');
        $this->assertDatabaseHas('entitlements', ['order_id' => $order->id, 'status' => EntitlementStatus::Refunded->value]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'refunded']);
    }

    public function test_flutterwave_webhook_rejects_invalid_signature(): void
    {
        $this->postJson(route('payments.flutterwave.webhook'), ['data' => ['tx_ref' => 'unknown']])->assertUnauthorized();
    }

    public function test_admin_can_upload_a_private_product_asset_and_customer_portal_renders(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->product(['availability' => 'available']);
        $this->actingAs($admin)->post(route('admin.products.assets.store', $product), ['file' => UploadedFile::fake()->create('journal.pdf', 20, 'application/pdf'), 'download_policy' => 'current_and_future'])->assertRedirect();
        $this->assertDatabaseHas('product_assets', ['product_id' => $product->id, 'extension' => 'pdf', 'disk' => 'local']);
        $customer = $this->customer();
        $this->actingAs($customer)->get(route('account.dashboard'))->assertOk()->assertSee('Welcome');
        $this->actingAs($customer)->get(route('account.orders'))->assertOk()->assertSee('No orders yet');
    }

    private function customer(array $overrides = []): User { return User::factory()->create(array_merge(['email_verified_at' => now(), 'is_admin' => false], $overrides)); }
    private function product(array $overrides = []): Product { return Product::create(array_merge(['name' => 'Test product '.uniqid(), 'slug' => 'test-product-'.uniqid(), 'short_description' => 'A test product.', 'availability' => 'available', 'currency' => 'TZS'], $overrides)); }
    private function course(array $overrides = []): Course { return Course::create(array_merge(['title' => 'Test course '.uniqid(), 'slug' => 'test-course-'.uniqid(), 'short_description' => 'A test course.', 'status' => 'published', 'enrollment_available' => true, 'currency' => 'TZS'], $overrides)); }
}
