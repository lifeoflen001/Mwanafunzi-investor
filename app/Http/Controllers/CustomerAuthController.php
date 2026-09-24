<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function login() { return view('auth.login'); }
    public function register() { return view('auth.register'); }
    public function verificationNotice() { return view('auth.verify'); }

    public function storeLogin(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) return back()->withErrors(['email' => 'Those credentials were not recognised.'])->onlyInput('email');
        $request->session()->regenerate();
        return to_route('account.dashboard');
    }

    public function storeRegister(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190', 'unique:users,email'], 'password' => ['required', 'confirmed', 'min:10'], 'phone' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'size:2'], 'terms' => ['accepted']]);
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'], 'phone' => $data['phone'] ?? null, 'country' => strtoupper($data['country'] ?? ''), 'status' => 'active', 'is_admin' => false]);
        Auth::login($user);
        $user->sendEmailVerificationNotification();
        return to_route('verification.notice')->with('success', 'Your account was created. Check your email to verify it before checkout.');
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) return to_route('account.dashboard');
        if ($request->user()->markEmailAsVerified()) event(new Verified($request->user()));
        return to_route('account.dashboard')->with('success', 'Your email is verified.');
    }

    public function resendVerification(Request $request)
    {
        abort_if($request->user()->hasVerifiedEmail(), 400);
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'A fresh verification link has been sent.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login');
    }
}
