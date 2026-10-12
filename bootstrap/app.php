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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.mfa' => \App\Http\Middleware\EnsureAdminMfa::class,
        ]);
        $middleware->redirectGuestsTo(fn ($request) => $request->routeIs('admin.*') ? \route('admin.login') : \route('login'));
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
                ? $referer
                : route('home');

            return redirect()->to($destination)->with('newsletter_status', 'rate-limited');
        });
    })->create();
