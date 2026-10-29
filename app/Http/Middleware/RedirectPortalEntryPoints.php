<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalEntryPoints
{
    public function handle(Request $request, Closure $next): Response
    {
        // Never forward submitted credentials or other request bodies to another host.
        if (! config('portals.enabled') || ! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $publicHost = strtolower((string) parse_url(config('portals.public_url'), PHP_URL_HOST));
        $adminHost = strtolower((string) parse_url(config('portals.admin_url'), PHP_URL_HOST));
        $host = strtolower($request->getHost());
        $path = '/'.$request->path();
        $destination = null;

        if (in_array($host, [$publicHost, 'www.'.$publicHost], true)) {
            if (in_array($path, ['/login', '/register', '/forgot-password'], true)) {
                $destination = rtrim(config('portals.client_url'), '/').$path;
            } elseif ($request->is('admin', 'admin/*')) {
                $destination = rtrim(config('portals.admin_url'), '/')
                    .($path === '/admin/login' ? '/login' : $path);
            }
        } elseif ($host === $adminHost && $path === '/admin/login') {
            $destination = rtrim(config('portals.admin_url'), '/').'/login';
        }

        if ($destination !== null) {
            $query = $request->getQueryString();

            return redirect()->away($destination.($query ? '?'.$query : ''), 302);
        }

        return $next($request);
    }
}
