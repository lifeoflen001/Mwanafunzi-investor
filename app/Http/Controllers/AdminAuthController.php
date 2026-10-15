<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use App\Rules\Recaptcha;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AdminAuthController extends Controller
{
    public function create() { return view('admin.auth.login'); }

    public function forgotPassword() { return view('admin.auth.password-email'); }

    public function resetPassword(string $token) { return view('admin.auth.password-reset', compact('token')); }

    public function sendPasswordReset(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'recaptcha_token' => Recaptcha::rules('password_reset'),
        ]);

        $admin = User::query()
            ->where('email', $data['email'])
            ->where('is_admin', true)
            ->where('status', 'active')
            ->first();

        if ($admin) {
            $token = Password::broker()->createToken($admin);
            $admin->notify(new AdminResetPasswordNotification($token));
        }

        return back()->with('success', 'If that email belongs to an active administrator account, a password reset link has been sent.');
    }

    public function updatePasswordFromReset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:10'],
            'recaptcha_token' => Recaptcha::rules('password_reset'),
        ]);

        $admin = User::query()
            ->where('email', $data['email'])
            ->where('is_admin', true)
            ->where('status', 'active')
            ->first();

        if (! $admin) {
            return back()->withErrors(['email' => 'This administrator password reset link is invalid or has expired.'])->withInput($request->only('email'));
        }

        $status = Password::broker()->reset(
            [
                'email' => $admin->email,
                'password' => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $data['token'],
            ],
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password, 'remember_token' => null])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'This administrator password reset link is invalid or has expired.'])->withInput($request->only('email'));
        }

        AdminAudit::record('security.admin_password_reset', 'Administrator password was reset successfully');

        return to_route('admin.login')->with('success', 'Your administrator password has been reset. Sign in with the new password.');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'recaptcha_token' => Recaptcha::rules('admin_login')]);
        if (Auth::attempt([...$credentials, 'is_admin' => true, 'status' => 'active'], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->user()->forceFill(['last_login_at' => now()])->saveQuietly();
            AdminAudit::record('security.admin_login', 'Administrator password authentication succeeded');
            if ($request->user()->admin_mfa_enabled) return to_route('admin.mfa.challenge');
            if (config('security.admin_mfa_required')) return to_route('admin.mfa.setup');
            return to_route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('admin.login');
    }
}
