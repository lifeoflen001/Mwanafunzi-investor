<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Order;
use App\Services\DownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerAccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        return view('account.dashboard', [
            'orders' => $user->orders()->withCount('items')->latest()->limit(5)->get(),
            'orderCount' => $user->orders()->count(),
            'downloads' => $user->entitlements()->where('type', 'product')->where('status', 'active')->with('product.assets')->latest()->get(),
            'downloadCount' => $user->entitlements()->where('type', 'product')->where('status', 'active')->count(),
            'enrollments' => $user->enrollments()->with('course')->whereIn('status', ['active', 'completed'])->latest()->get(),
            'courseCount' => $user->enrollments()->whereIn('status', ['active', 'completed'])->count(),
        ]);
    }
    public function orders(Request $request) { return view('account.orders', ['orders' => $request->user()->orders()->withCount('items')->latest()->paginate(15)]); }
    public function order(Order $order) { $this->owner($order); return view('account.order', compact('order')); }
    public function downloads(Request $request) { return view('account.downloads', ['entitlements' => $request->user()->entitlements()->where('type', 'product')->where('status', 'active')->with(['product.assets', 'version'])->latest()->get(), 'downloads' => app(DownloadService::class)]); }
    public function courses(Request $request) { return view('account.courses', ['enrollments' => $request->user()->enrollments()->with(['course.modules.lessons'])->latest()->paginate(12)]); }
    public function course(Request $request, Enrollment $enrollment) { abort_unless($enrollment->user_id === $request->user()->id, 403); return view('account.course', compact('enrollment')); }
    public function profile(Request $request) { return view('account.profile', ['user' => $request->user()]); }
    public function updateProfile(Request $request) { $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'phone' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'size:2']]); $request->user()->update([...$data, 'country' => strtoupper($data['country'] ?? '')]); return back()->with('success', 'Profile updated.'); }
    public function updateAvatar(Request $request)
    {
        $data = $request->validate(['avatar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096']]);
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $user->update(['avatar_path' => $data['avatar']->store('customer-avatars', 'public')]);
        return back()->with('success', 'Profile photo updated.');
    }
    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $user->update(['avatar_path' => null]);
        return back()->with('success', 'Profile photo removed.');
    }
    public function security() { return view('account.security'); }
    public function updatePassword(Request $request) { $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', 'min:10']]); $request->user()->update(['password' => $data['password']]); return back()->with('success', 'Password updated.'); }
    public function receipt(Order $order) { $this->owner($order); return view('commerce.receipt', compact('order')); }
    private function owner(Order $order): void { abort_unless($order->user_id === auth()->id(), 403); }
}
