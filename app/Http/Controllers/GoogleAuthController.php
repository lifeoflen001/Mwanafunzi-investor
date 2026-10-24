<?php

namespace App\Http\Controllers;

use App\Models\SocialIdentity;
use App\Models\User;
use App\Services\AuthenticationRedirectService;
use App\Services\GoogleIdentityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use LogicException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        return $this->start($request, false);
    }

    public function linkRedirect(Request $request)
    {
        abort_unless($request->user() && ! $request->user()->is_admin, 403);

        return $this->start($request, true);
    }

    public function callback(Request $request, GoogleIdentityService $identities, AuthenticationRedirectService $redirects)
    {
        $intent = $request->session()->pull('google.auth.intent', 'login');
        $defaultDestination = $intent === 'link' ? route('account.security') : $redirects->defaultDestination();
        $destination = $redirects->pull($request, $defaultDestination);
        $failureRoute = $intent === 'link' ? 'account.security' : 'login';

        if ($request->filled('error')) {
            return $this->googleFailure($request, $redirects, $failureRoute, $destination, 'Google sign-in was cancelled. You can continue with email and password.');
        }

        try {
            $google = Socialite::driver('google')
                ->scopes(['openid', 'email', 'profile'])
                ->enablePKCE()
                ->user();
        } catch (InvalidStateException) {
            return $this->googleFailure($request, $redirects, $failureRoute, $destination, 'That Google sign-in session expired. Please try again.');
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed.', ['exception' => $exception::class]);

            return $this->googleFailure($request, $redirects, $failureRoute, $destination, 'Google sign-in could not be completed. Please try again or use email and password.');
        }

        $claims = $identities->claims(
            $google->getRaw(),
            trim((string) $google->getId()),
            strtolower(trim((string) $google->getEmail())),
            trim((string) ($google->getName() ?: $google->getNickname() ?: 'Mwanafunzi client')),
            $google->getAvatar(),
        );

        if ($claims['provider_user_id'] === '' || ! filter_var($claims['provider_email'], FILTER_VALIDATE_EMAIL) || ! $claims['email_verified']) {
            return $this->googleFailure($request, $redirects, $failureRoute, $destination, 'Google did not provide a verified email identity, so no account was changed.');
        }

        if ($intent === 'link') {
            return $this->linkIdentity($request, $identities, $claims, $destination);
        }

        $identity = SocialIdentity::with('user')
            ->where('provider', 'google')
            ->where('provider_user_id', $claims['provider_user_id'])
            ->first();

        if ($identity) {
            return $this->signIn($request, $identity->user, $destination);
        }

        $existingUser = User::where('email', $claims['provider_email'])->first();
        if ($existingUser) {
            if ($existingUser->is_admin) {
                return to_route('login')->withErrors(['google' => 'Administrator accounts must use the administrator sign-in.']);
            }

            $request->session()->put('google.pending_identity', $claims);
            $redirects->remember($request, $destination);

            return to_route('login')->with('success', 'This email already has a client account. Sign in with your password to connect Google securely.');
        }

        $user = User::create([
            'name' => $claims['name'],
            'email' => $claims['provider_email'],
            'password' => null,
            'email_verified_at' => now(),
            'status' => 'active',
            'is_admin' => false,
        ]);
        $identities->link($user, $claims);

        return $this->signIn($request, $user, $destination);
    }

    public function disconnect(Request $request)
    {
        $user = $request->user();
        abort_unless($user && ! $user->is_admin, 403);

        if ($user->password === null) {
            return back()->withErrors(['google' => 'Set a password before disconnecting your only sign-in method.']);
        }

        $user->socialIdentities()->where('provider', 'google')->delete();

        return back()->with('success', 'Google sign-in has been disconnected.');
    }

    private function start(Request $request, bool $link)
    {
        if (! filled(config('services.google.client_id')) || ! filled(config('services.google.client_secret'))) {
            $route = $link ? 'account.security' : 'login';

            return to_route($route)->withErrors(['google' => 'Google sign-in is not configured yet. Use email and password for now.']);
        }

        $request->session()->put('google.auth.intent', $link ? 'link' : 'login');
        $redirects = app(AuthenticationRedirectService::class);
        $redirects->remember($request, $request->query('redirect') ?: ($link ? route('account.security') : $redirects->defaultDestination()));

        if (! $link) {
            $request->session()->forget('google.pending_identity');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->enablePKCE()
            ->redirect();
    }

    private function linkIdentity(Request $request, GoogleIdentityService $identities, array $claims, string $destination)
    {
        $user = $request->user();
        if (! $user || $user->is_admin) {
            return to_route('login')->withErrors(['google' => 'Sign in to a client account before connecting Google.']);
        }

        try {
            $identities->link($user, $claims);
        } catch (LogicException $exception) {
            return to_route('account.security')->withErrors(['google' => $exception->getMessage()]);
        }

        return redirect()->to($destination)->with('success', 'Google sign-in is now connected to your client account.');
    }

    private function signIn(Request $request, User $user, string $destination)
    {
        if ($user->is_admin || ($user->status ?: 'active') !== 'active') {
            return to_route('login')->withErrors(['google' => 'This client account is not available for sign-in.']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->to($destination);
    }

    private function googleFailure(Request $request, AuthenticationRedirectService $redirects, string $route, string $destination, string $message)
    {
        $redirects->remember($request, $destination);

        return to_route($route)->withErrors(['google' => $message]);
    }
}
