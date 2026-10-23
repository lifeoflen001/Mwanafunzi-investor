<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $cspNonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $cspNonce);
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
            "script-src 'self' 'nonce-{$cspNonce}' https://embed.tawk.to https://*.tawk.to https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/".$viteDevSources,
            "style-src 'self' 'unsafe-inline' https: https://*.tawk.to".$viteDevSources,
            "style-src-attr 'unsafe-inline'",
            "img-src 'self' data: blob: https: tawk.link https://*.tawk.to",
            "font-src 'self' data: https: https://*.tawk.to",
            "connect-src 'self' ws: wss: https://*.tawk.to https://www.google.com/recaptcha/".$viteDevSources,
            "frame-src 'self' https://*.flutterwave.com https://*.tawk.to",
            "form-action 'self' https://*.flutterwave.com https://*.tawk.to",
        ]));
        if ($request->user() && ($request->is('account') || $request->is('account/*') || $request->is('admin') || $request->is('admin/*') || $request->is('checkout/*') || $request->is('payments/*') || $request->is('downloads/*'))) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        if ($request->isSecure() || config('security.force_hsts', false)) $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
