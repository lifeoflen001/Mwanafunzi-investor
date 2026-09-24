<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CommerceController extends Controller
{
    public function productCheckout(Product $product) { abort_unless($product->isPurchasable(), 404); return view('commerce.checkout', ['purchasable' => $product, 'kind' => 'product']); }
    public function courseCheckout(Course $course) { abort_unless($course->isPublishedForPurchase() && $course->enrollment_available, 404); return view('commerce.checkout', ['purchasable' => $course, 'kind' => 'course']); }

    public function storeProduct(Request $request, Product $product) { return $this->store($request, $product, 'product'); }
    public function storeCourse(Request $request, Course $course) { return $this->store($request, $course, 'course'); }

    public function success(Order $order)
    {
        $this->owner($order);
        return view('commerce.success', compact('order'));
    }

    public function failure(Order $order)
    {
        $this->owner($order);
        return view('commerce.failure', compact('order'));
    }

    public function processing(Order $order)
    {
        $this->owner($order);
        return view('commerce.processing', compact('order'));
    }

    public function sandbox(Payment $payment)
    {
        $this->owner($payment->order);
        abort_unless($payment->provider === 'sandbox' && $payment->status->value !== 'paid', 404);
        return view('commerce.sandbox', compact('payment'));
    }

    public function completeSandbox(Request $request, Payment $payment, PaymentService $payments)
    {
        $this->owner($payment->order);
        $data = $request->validate(['result' => ['required', 'in:successful,failed,cancelled']]);
        if ($data['result'] !== 'successful') { $payments->fail($payment, 'Sandbox payment was not completed.'); return to_route('payments.failure', $payment->order); }
        $gateway = app(PaymentGatewayManager::class)->gateway('sandbox');
        $verified = $gateway->verifyPayment($payment, null, ['status' => 'successful']);
        $payments->verifyAndComplete($payment, $verified, 'sandbox-'.$payment->id.'-successful');
        return to_route('payments.success', $payment->order);
    }

    public function flutterwaveReturn(Request $request, PaymentService $payments, PaymentGatewayManager $gateways)
    {
        $payment = Payment::where('internal_reference', $request->string('tx_ref'))->firstOrFail();
        $this->owner($payment->order);
        if ($request->string('status')->toString() !== 'successful') { $payments->fail($payment, 'Payment was cancelled or failed at the provider.'); return to_route('payments.failure', $payment->order); }
        try { $verified = $gateways->gateway($payment->provider)->verifyPayment($payment, $request->string('transaction_id')->toString()); $payments->verifyAndComplete($payment, $verified, 'return-'.$payment->id.'-'.$request->string('transaction_id')); return to_route('payments.success', $payment->order); }
        catch (RuntimeException $e) { Log::warning('Payment verification is pending or failed.', ['order' => $payment->order->order_number, 'provider' => $payment->provider]); return to_route('payments.processing', $payment->order); }
    }

    public function flutterwaveWebhook(Request $request, PaymentGatewayManager $gateways, PaymentService $payments)
    {
        $gateway = $gateways->gateway('flutterwave');
        if (! $gateway->validateWebhook($request->headers->all(), $request->getContent())) abort(401);
        $payload = $request->json()->all();
        $data = $payload['data'] ?? [];
        $payment = Payment::where('internal_reference', $data['tx_ref'] ?? null)->first();
        if (! $payment) return response()->json(['received' => true]);
        $eventKey = 'flutterwave-'.($payload['id'] ?? sha1($request->getContent()));
        try { $verified = $gateway->verifyPayment($payment, (string) ($data['id'] ?? '')); $payments->verifyAndComplete($payment, $verified, $eventKey); return response()->json(['received' => true]); }
        catch (RuntimeException $e) { Log::warning('Flutterwave webhook could not be verified.', ['event' => $eventKey, 'order' => $payment->order->order_number]); return response()->json(['received' => false], 202); }
    }

    private function store(Request $request, Product|Course $purchasable, string $kind)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190'], 'phone' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'size:2'], 'terms' => ['accepted']]);
        try {
            $order = $kind === 'product' ? app(CheckoutService::class)->createForProduct($request->user(), $purchasable, $data) : app(CheckoutService::class)->createForCourse($request->user(), $purchasable, $data);
            $result = app(CheckoutService::class)->initialize($order, route('payments.flutterwave.return'));
            return redirect()->to($result['checkout_url']);
        } catch (RuntimeException $e) { return back()->withInput()->withErrors(['checkout' => $e->getMessage()]); }
    }

    private function owner(Order $order): void { abort_unless($order->user_id === auth()->id(), 403); }
}
