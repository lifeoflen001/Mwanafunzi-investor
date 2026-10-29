<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([__DIR__.'/../app/Console/Commands'])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\RequestContext::class);
        $middleware->append(\App\Http\Middleware\RedirectPortalEntryPoints::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.mfa' => \App\Http\Middleware\EnsureAdminMfa::class,
            'customer' => \App\Http\Middleware\EnsureCustomer::class,
            'client.mfa' => \App\Http\Middleware\EnsureClientMfa::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->routeIs('admin.*')) {
                return \route('admin.login');
            }

            $redirects = app(\App\Services\AuthenticationRedirectService::class);
            $destination = $redirects->captureGuestRequest($request);

            return \route('login', ['redirect' => $destination]);
        });
        $middleware->validateCsrfTokens(except: ['payments/flutterwave/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TooManyRequestsHttpException $exception, $request) {
            if (! $request->routeIs('newsletter.subscribe')) {
                return null;
            }

            $referer = (string) $request->headers->get('referer');
            $refererPath = $referer ? (string) parse_url($referer, PHP_URL_PATH) : '';
            $destination = $referer && $refererPath !== '/newsletter/subscribe'
                ? \App\Support\SafeRedirect::normalize($refererPath, route('home'))
                : route('home');

            return redirect()->to($destination)->with('newsletter_status', 'rate-limited');
        });
    })->create();
