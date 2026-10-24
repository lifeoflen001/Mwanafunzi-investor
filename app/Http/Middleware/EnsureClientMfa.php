<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientMfa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $mfa = $user?->clientTwoFactorAuthentication;

        if (! $user || ! $mfa?->enabled() || (int) $request->session()->get('client_mfa_verified_user_id') === (int) $user->id) {
            return $next($request);
        }

        $request->session()->put('client_mfa_intended_url', $request->getRequestUri());

        return to_route('account.security.challenge');
    }
}
