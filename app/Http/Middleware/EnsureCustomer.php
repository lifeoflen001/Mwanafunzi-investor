<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user !== null
                && ! $user->is_admin
                && in_array($user->status ?: 'active', ['active'], true),
            403,
        );

        return $next($request);
    }
}
