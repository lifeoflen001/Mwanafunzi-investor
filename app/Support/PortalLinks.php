<?php

namespace App\Support;

use Illuminate\Http\Request;

final class PortalLinks
{
    public static function clientLogin(array $parameters = []): string
    {
        return self::url('client', '/login', $parameters, 'login');
    }

    public static function adminLogin(): string
    {
        return self::url('admin', '/login', [], 'admin.login');
    }

    /**
     * Keep the OAuth callback on the client host so the state/PKCE session
     * cookie created during sign-in remains available to the callback.
     */
    public static function googleCallbackFor(Request $request): ?string
    {
        if (! config('portals.enabled')) {
            return null;
        }

        $clientHost = strtolower((string) parse_url(config('portals.client_url'), PHP_URL_HOST));

        return strtolower($request->getHost()) === $clientHost
            ? rtrim(config('portals.client_url'), '/').'/auth/google/callback'
            : null;
    }

    private static function url(string $portal, string $path, array $parameters, string $fallback): string
    {
        if (! config('portals.enabled')) {
            return route($fallback, $parameters);
        }

        return rtrim(config('portals.'.$portal.'_url'), '/').$path
            .($parameters ? '?'.http_build_query($parameters, '', '&', PHP_QUERY_RFC3986) : '');
    }
}
