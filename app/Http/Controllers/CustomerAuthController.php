<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CustomerAuthController extends Controller
{
    public function login() { return view('auth.login'); }
    public function register() { return view('auth.register'); }
    public function verificationNotice() { return view('auth.verify'); }
    public function forgotPassword() { return view('auth.password-email'); }
    public function resetPassword(string $token) { return view('auth.password-reset', compact('token')); }

    public function sendPasswordReset(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email'], 'recaptcha_token' => Recaptcha::rules('password_reset')]);
        $user = User::where('email', $data['email'])->where('is_admin', false)->first();
        if ($user) Password::sendResetLink(['email' => $user->email]);

        return back()->with('success', 'If that email belongs to an active client account, a password reset link has been sent.');
    }

    public function updatePasswordFromReset(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'string'], 'email' => ['required', 'email'], 'password' => ['required', 'confirmed', 'min:10'], 'recaptcha_token' => Recaptcha::rules('password_reset')]);
        $status = Password::reset($data, function (User $user, string $password): void {
            if ($user->is_admin) return;
            $user->forceFill(['password' => $password, 'remember_token' => null])->save();
        });
        if ($status !== Password::PASSWORD_RESET) return back()->withErrors(['email' => 'This password reset link is invalid or has expired.'])->withInput($request->only('email'));

        return to_route('login')->with('success', 'Your password has been reset. You can now sign in.');
    }

    public function storeLogin(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'recaptcha_token' => Recaptcha::rules('login')]);
        if (! Auth::attempt([...$credentials, 'is_admin' => false, 'status' => 'active'], $request->boolean('remember'))) return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        $request->session()->regenerate();
        return to_route('account.dashboard');
    }

    public function storeRegister(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190', 'unique:users,email'], 'password' => ['required', 'confirmed', 'min:10'], 'phone' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'size:2'], 'terms' => ['accepted'], 'recaptcha_token' => Recaptcha::rules('register')]);
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
