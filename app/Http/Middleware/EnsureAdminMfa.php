<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMfa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user?->is_admin || $request->routeIs('admin.mfa.*')) return $next($request);
        if (! $user->admin_mfa_enabled) {
            return config('security.admin_mfa_required') ? to_route('admin.mfa.setup') : $next($request);
        }
        if ((int) $request->session()->get('admin_mfa_verified_user_id') !== (int) $user->id) return to_route('admin.mfa.challenge');

        return $next($request);
    }
}
