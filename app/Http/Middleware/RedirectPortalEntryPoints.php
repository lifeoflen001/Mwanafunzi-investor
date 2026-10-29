<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalEntryPoints
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('portals.enabled')) {
            return $next($request);
        }

        $publicHost = strtolower((string) parse_url(config('portals.public_url'), PHP_URL_HOST));
        $clientHost = strtolower((string) parse_url(config('portals.client_url'), PHP_URL_HOST));
        $adminHost = strtolower((string) parse_url(config('portals.admin_url'), PHP_URL_HOST));
        $host = strtolower($request->getHost());
        $trimmedPath = trim($request->path(), '/');
        $path = $trimmedPath === '' ? '/' : '/'.$trimmedPath;

        if (in_array($host, [$publicHost, 'www.'.$publicHost], true)) {
            return $this->handlePublic($request, $next, $path);
        }

        if ($host === $clientHost) {
            return $this->handleClient($request, $next, $path);
        }

        if ($host === $adminHost) {
            return $this->handleAdmin($request, $next, $path);
        }

        return $next($request);
    }

    private function handlePublic(Request $request, Closure $next, string $path): Response
    {
        if (! $this->isReadRequest($request)) {
            return $next($request);
        }

        if (in_array($path, ['/login', '/register', '/forgot-password'], true)) {
            return $this->redirect($request, config('portals.client_url'), $path);
        }

        if ($this->isAdminPath($path)) {
            return $this->redirect($request, config('portals.admin_url'), $path === '/admin/login' ? '/login' : $path);
        }

        return $next($request);
    }

    private function handleClient(Request $request, Closure $next, string $path): Response
    {
        if ($path === '/') {
            return $this->redirect($request, config('portals.client_url'), '/login');
        }

        if ($this->isAdminPath($path)) {
            return $this->redirectOrReject($request, config('portals.admin_url'), $path === '/admin/login' ? '/login' : $path);
        }

        if ($this->isClientPath($path)) {
            return $next($request);
        }

        return $this->redirectOrReject($request, config('portals.public_url'), $path);
    }

    private function handleAdmin(Request $request, Closure $next, string $path): Response
    {
        if ($path === '/') {
            return $this->redirect($request, config('portals.admin_url'), '/login');
        }

        if ($path === '/admin/login' && $this->isReadRequest($request)) {
            return $this->redirect($request, config('portals.admin_url'), '/login');
        }

        if ($path === '/login' || $this->isAdminPath($path)) {
            return $next($request);
        }

        if ($this->isClientPath($path)) {
            return $this->redirectOrReject($request, config('portals.client_url'), $path);
        }

        return $this->redirectOrReject($request, config('portals.public_url'), $path);
    }

    private function isClientPath(string $path): bool
    {
        return in_array($path, ['/login', '/register', '/forgot-password', '/logout', '/email/verify', '/email/verification-notification'], true)
            || str_starts_with($path, '/reset-password/')
            || str_starts_with($path, '/auth/google/')
            || str_starts_with($path, '/email/verify/')
            || str_starts_with($path, '/account')
            || str_starts_with($path, '/checkout/')
            || str_starts_with($path, '/payments/')
            || str_starts_with($path, '/downloads/');
    }

    private function isAdminPath(string $path): bool
    {
        return $path === '/admin' || str_starts_with($path, '/admin/');
    }

    private function isReadRequest(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD'], true);
    }

    private function redirectOrReject(Request $request, string $baseUrl, string $path): Response
    {
        return $this->isReadRequest($request)
            ? $this->redirect($request, $baseUrl, $path)
            : response('', 404);
    }

    private function redirect(Request $request, string $baseUrl, string $path): Response
    {
        $destination = rtrim($baseUrl, '/').$path;
        $query = $request->getQueryString();

        return redirect()->away($destination.($query ? '?'.$query : ''), 302);
    }
}
