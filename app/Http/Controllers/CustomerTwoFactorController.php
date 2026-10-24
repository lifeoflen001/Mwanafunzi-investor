<?php

namespace App\Http\Controllers;

use App\Models\ClientTwoFactorAuthentication;
use App\Support\SafeRedirect;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CustomerTwoFactorController extends Controller
{
    public function setup(Request $request)
    {
        if ($request->user()->clientTwoFactorAuthentication?->enabled()) {
            return to_route('account.security');
        }

        $secret = $request->session()->get('client_2fa_setup_secret');
        if (! $secret) {
            $secret = Totp::secret();
            $request->session()->put('client_2fa_setup_secret', $secret);
        }

        return view('account.security.2fa-setup', [
            'secret' => $secret,
            'provisioningUri' => Totp::provisioningUri($secret, $request->user()->email),
        ]);
    }

    public function storeSetup(Request $request)
    {
        $rules = ['code' => ['required', 'digits:6']];
        if ($request->user()->password !== null) {
            $rules['current_password'] = ['required', 'current_password'];
        }
        $data = $request->validate($rules);
        $secret = $request->session()->get('client_2fa_setup_secret');
        abort_unless($secret, 419);
        $this->ensureWithinRateLimit($request, 'setup');

        if (! Totp::verify($secret, $data['code'], null, config('security.client_mfa_window', 1))) {
            return back()->withErrors(['code' => 'That authenticator code is not valid.'])->withInput();
        }

        $codes = collect(range(1, (int) config('security.client_mfa_recovery_codes', 8)))
            ->map(fn () => strtoupper(Str::random(4).'-'.Str::random(4)))
            ->all();

        ClientTwoFactorAuthentication::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'secret' => $secret,
                'recovery_codes' => array_map(fn ($code) => Hash::make($code), $codes),
                'confirmed_at' => now(),
            ],
        );

        $request->session()->forget('client_2fa_setup_secret');
        $request->session()->put('client_mfa_verified_user_id', $request->user()->id);

        return to_route('account.security')->with('success', 'Two-factor authentication is enabled. Save these recovery codes in a password manager.')->with('recovery_codes', $codes);
    }

    public function challenge(Request $request)
    {
        abort_unless($request->user()->clientTwoFactorAuthentication?->enabled(), 404);

        return view('account.security.2fa-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:64']]);
        $this->ensureWithinRateLimit($request, 'challenge');
        $user = $request->user();
        $mfa = $user->clientTwoFactorAuthentication;
        abort_unless($mfa?->enabled(), 404);

        $valid = Totp::verify((string) $mfa->secret, $data['code'], null, config('security.client_mfa_window', 1));
        if (! $valid) {
            foreach ((array) $mfa->recovery_codes as $index => $hash) {
                if (Hash::check(strtoupper(trim($data['code'])), $hash)) {
                    $remaining = $mfa->recovery_codes;
                    unset($remaining[$index]);
                    $mfa->update(['recovery_codes' => array_values($remaining)]);
                    $valid = true;
                    break;
                }
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'The code is invalid or has already been used.']);
        }

        RateLimiter::clear($this->rateKey($request, 'challenge'));
        $request->session()->regenerate();
        $request->session()->put('client_mfa_verified_user_id', $user->id);
        $destination = SafeRedirect::normalize($request->session()->pull('client_mfa_intended_url'), route('account.dashboard'));

        return redirect()->to($destination);
    }

    public function disable(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'code' => ['required', 'digits:6'],
        ]);
        $mfa = $request->user()->clientTwoFactorAuthentication;
        abort_unless($mfa?->enabled() && Totp::verify((string) $mfa->secret, $data['code'], null, config('security.client_mfa_window', 1)), 422, 'A current authenticator code is required to disable two-factor authentication.');
        $mfa->delete();
        $request->session()->forget('client_mfa_verified_user_id');

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }

    private function ensureWithinRateLimit(Request $request, string $action): void
    {
        $key = $this->rateKey($request, $action);
        abort_if(RateLimiter::tooManyAttempts($key, 5), 429, 'Too many two-factor attempts. Please try again later.');
        RateLimiter::hit($key, 60);
    }

    private function rateKey(Request $request, string $action): string
    {
        return 'client-mfa:'.$action.':'.$request->user()->id.':'.$request->ip();
    }
}
