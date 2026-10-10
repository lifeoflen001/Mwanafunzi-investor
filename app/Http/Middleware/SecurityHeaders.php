<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $viteDevSources = app()->environment(['local', 'testing']) ? ' http://localhost:5173 http://127.0.0.1:5173' : '';
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self' https://*.flutterwave.com",
            "script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/".$viteDevSources,
            "style-src 'self' 'unsafe-inline' https:".$viteDevSources,
            "style-src-attr 'unsafe-inline'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https:",
            "connect-src 'self' ws: wss: https://www.google.com/recaptcha/".$viteDevSources,
            "frame-src 'self' https://*.flutterwave.com",
        ]));
        if ($request->isSecure() || config('security.force_hsts', false)) $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
