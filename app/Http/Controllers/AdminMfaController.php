<?php

namespace App\Http\Controllers;

use App\Support\AdminAudit;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminMfaController extends Controller
{
    public function setup(Request $request)
    {
        if ($request->user()->admin_mfa_enabled) return to_route('admin.mfa.challenge');
        $secret = $request->session()->get('admin_mfa_setup_secret');
        if (! $secret) {
            $secret = Totp::secret();
            $request->session()->put('admin_mfa_setup_secret', $secret);
        }

        return view('admin.auth.mfa-setup', ['secret' => $secret, 'provisioningUri' => Totp::provisioningUri($secret, $request->user()->email)]);
    }

    public function storeSetup(Request $request)
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'code' => ['required', 'digits:6']]);
        $secret = $request->session()->get('admin_mfa_setup_secret');
        abort_unless($secret, 419);
        $this->ensureWithinRateLimit($request, 'setup');
        if (! Totp::verify($secret, $data['code'])) return back()->withErrors(['code' => 'That authenticator code is not valid.'])->withInput();

        $codes = collect(range(1, (int) config('security.admin_mfa_recovery_codes', 8)))->map(fn () => strtoupper(Str::random(4).'-'.Str::random(4)))->all();
        $request->user()->forceFill([
            'admin_mfa_secret' => $secret,
            'admin_mfa_enabled' => true,
            'admin_mfa_recovery_codes' => array_map(fn ($code) => Hash::make($code), $codes),
            'admin_mfa_confirmed_at' => now(),
        ])->save();
        $request->session()->forget('admin_mfa_setup_secret');
        $request->session()->put('admin_mfa_verified_user_id', $request->user()->id);
        AdminAudit::record('security.mfa_enabled', 'Enabled administrator MFA');

        return to_route('admin.profile')->with('success', 'MFA is enabled. Save these recovery codes in a password manager.')->with('recovery_codes', $codes);
    }

    public function challenge()
    {
        if (! auth()->user()?->admin_mfa_enabled) return to_route('admin.mfa.setup');

        return view('admin.auth.mfa-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:64']]);
        $this->ensureWithinRateLimit($request, 'challenge');
        $user = $request->user();
        if (Totp::verify((string) $user->admin_mfa_secret, $data['code'])) {
            $this->completeChallenge($request);
            return to_route('admin.dashboard');
        }
        foreach ((array) $user->admin_mfa_recovery_codes as $index => $hash) {
            if (Hash::check(strtoupper(trim($data['code'])), $hash)) {
                $remaining = $user->admin_mfa_recovery_codes;
                unset($remaining[$index]);
                $user->forceFill(['admin_mfa_recovery_codes' => array_values($remaining)])->save();
                AdminAudit::record('security.mfa_recovery_used', 'Used an administrator MFA recovery code');
                $this->completeChallenge($request);
                return to_route('admin.dashboard');
            }
        }

        return back()->withErrors(['code' => 'The code is invalid or has already been used.']);
    }

    public function disable(Request $request)
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'code' => ['required', 'digits:6']]);
        $user = $request->user();
        abort_unless($user->admin_mfa_enabled, 404);
        abort_unless(Totp::verify((string) $user->admin_mfa_secret, $data['code']), 422, 'A current authenticator code is required to disable MFA.');
        $user->forceFill(['admin_mfa_secret' => null, 'admin_mfa_enabled' => false, 'admin_mfa_recovery_codes' => null, 'admin_mfa_confirmed_at' => null])->save();
        $request->session()->forget('admin_mfa_verified_user_id');
        AdminAudit::record('security.mfa_disabled', 'Disabled administrator MFA');

        return back()->with('success', 'MFA has been disabled.');
    }

    private function completeChallenge(Request $request): void
    {
        RateLimiter::clear($this->rateKey($request, 'challenge'));
        $request->session()->regenerate();
        $request->session()->put('admin_mfa_verified_user_id', $request->user()->id);
    }

    private function ensureWithinRateLimit(Request $request, string $action): void
    {
        $key = $this->rateKey($request, $action);
        abort_if(RateLimiter::tooManyAttempts($key, 5), 429, 'Too many MFA attempts. Please try again later.');
        RateLimiter::hit($key, 60);
    }

    private function rateKey(Request $request, string $action): string
    {
        return 'admin-mfa:'.$action.':'.$request->user()->id.':'.$request->ip();
    }
}
