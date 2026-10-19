<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_registered_controller_actions_are_real_methods(): void
    {
        $missing = collect(Route::getRoutes())->map(function ($route) {
            $uses = $route->getAction('uses');
            if (! is_string($uses) || ! Str::contains($uses, '@')) {
                return null;
            }

            [$class, $method] = explode('@', $uses, 2);

            return method_exists($class, $method) ? null : $route->getName().' → '.$uses;
        })->filter()->values()->all();

        $this->assertSame([], $missing, 'Registered controller actions must resolve to existing methods.');
    }

    public function test_public_system_routes_smoke_test(): void
    {
        foreach (['/', '/health', '/health/live', '/health/ready', '/robots.txt', '/sitemap.xml', '/site-map', '/contact', '/financial-academy', '/digital-software', '/creative-studio', '/journal', '/about', '/privacy-policy', '/terms', '/risk-disclosure', '/refund-policy', '/disclaimer', '/cookie-policy'] as $path) {
            $this->get($path)->assertSuccessful();
        }
    }
}
