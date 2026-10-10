<?php

namespace App\Http\Controllers;

use App\Rules\Recaptcha;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function create() { return view('admin.auth.login'); }

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
