<?php

namespace App\Http\Controllers;

use App\Models\CourseWaitlist;
use App\Models\Entitlement;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAsset;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCommerceController extends Controller
{
    public function dashboard() { return view('admin.commerce.dashboard', ['ordersToday' => Order::whereDate('created_at', today())->count(), 'paidOrders' => Order::whereIn('payment_status', ['paid', 'partially_refunded'])->count(), 'pendingPayments' => Order::whereIn('payment_status', ['pending', 'processing'])->count(), 'revenue' => Order::whereIn('payment_status', ['paid', 'partially_refunded'])->sum('total'), 'enrollments' => Enrollment::where('status', 'active')->count(), 'waitlists' => CourseWaitlist::where('status', 'active')->count()]); }
    public function orders(Request $request) { $orders = Order::withCount('items')->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', '%'.$request->q.'%')->orWhere('customer_email', 'like', '%'.$request->q.'%')))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->latest()->paginate(25)->withQueryString(); return view('admin.commerce.orders', compact('orders')); }
    public function order(Order $order) { return view('admin.commerce.order', ['order' => $order->load(['items', 'payments.events', 'entitlements', 'refunds'])]); }
    public function payments(Request $request) { $payments = Payment::with('order')->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->latest()->paginate(25)->withQueryString(); return view('admin.commerce.payments', compact('payments')); }
    public function entitlements() { return view('admin.commerce.entitlements', ['entitlements' => Entitlement::with(['user', 'product', 'course'])->latest()->paginate(25)]); }
    public function enrollments() { return view('admin.commerce.enrollments', ['enrollments' => Enrollment::with(['user', 'course', 'order'])->latest()->paginate(25)]); }
    public function waitlists(Request $request) { $waitlists = CourseWaitlist::with('course')->when($request->filled('course_id'), fn ($q) => $q->where('course_id', $request->course_id))->latest('joined_at')->paginate(25)->withQueryString(); return view('admin.commerce.waitlists', ['waitlists' => $waitlists]); }
    public function waitlistExport() { $rows = CourseWaitlist::with('course')->latest('joined_at')->get(); return response()->streamDownload(function () use ($rows) { $out = fopen('php://output', 'w'); fputcsv($out, ['Course', 'Name', 'Email', 'Joined At', 'Status']); foreach ($rows as $row) fputcsv($out, [$this->csv($row->course->title), $this->csv($row->name), $this->csv($row->email), $row->joined_at, $row->status]); fclose($out); }, 'course-waitlist.csv', ['Content-Type' => 'text/csv']); }
    public function refund(Request $request, Order $order, RefundService $refunds) { $data = $request->validate(['amount' => ['required', 'numeric', 'min:0.01'], 'reason' => ['required', 'string', 'max:500'], 'confirmed' => ['accepted']]); $refunds->markRefunded($order, $request->user(), (string) $data['amount'], $data['reason']); return back()->with('success', 'Refund recorded and access rules updated.'); }
    public function assetStore(Request $request, Product $product)
    {
        $data = $request->validate(['file' => ['required', 'file', 'max:51200', 'mimes:xlsx,xlsm,pdf,zip,csv'], 'name' => ['nullable', 'string', 'max:190'], 'product_version_id' => ['nullable', 'integer', 'exists:product_versions,id'], 'download_policy' => ['required', 'in:purchased_version,current_and_future']]);
        $file = $request->file('file'); $path = $file->store('commerce/'.$product->id, 'local');
        $product->assets()->create(['product_version_id' => $data['product_version_id'] ?? null, 'name' => ($data['name'] ?? null) ?: $file->getClientOriginalName(), 'path' => $path, 'disk' => 'local', 'mime_type' => $file->getMimeType(), 'extension' => strtolower($file->getClientOriginalExtension()), 'size' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()), 'download_policy' => $data['download_policy']]);
        return back()->with('success', 'Protected product asset uploaded.');
    }
    public function assetDestroy(ProductAsset $asset) { Storage::disk($asset->disk)->delete($asset->path); $asset->delete(); return back()->with('success', 'Product asset removed.'); }
    private function csv(string $value): string { return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value; }
}
