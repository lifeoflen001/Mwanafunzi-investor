<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->requestId($request->headers->get('X-Request-Id'));

        $request->attributes->set('request_id', $requestId);
        Log::withContext([
            'request_id' => $requestId,
            'method' => $request->method(),
            'route' => $request->route()?->getName() ?? $request->route()?->uri() ?? 'unmatched',
        ]);

        try {
            $response = $next($request);
        } finally {
            Log::withoutContext();
        }

        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }

    private function requestId(?string $candidate): string
    {
        $candidate = trim((string) $candidate);

        return preg_match('/\A[a-zA-Z0-9._:-]{1,64}\z/', $candidate)
            ? $candidate
            : (string) Str::ulid();
    }
}
