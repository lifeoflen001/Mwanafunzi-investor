<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Middleware\RedirectPortalEntryPoints;
use App\Support\PortalLinks;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PortalSubdomainTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['portals.enabled' => true]);
    }

    public function test_main_domain_login_moves_to_client_subdomain_and_preserves_query(): void
    {
        $this->get('https://mwanafunziinvestor.com/login?redirect=%2Faccount%2Fcourses')
            ->assertRedirect('https://client.mwanafunziinvestor.com/login?redirect=%2Faccount%2Fcourses');
    }

    public function test_main_domain_admin_login_moves_to_admin_login(): void
    {
        $this->get('https://mwanafunziinvestor.com/admin/login')
            ->assertRedirect('https://admin.mwanafunziinvestor.com/login');
        $this->get('https://www.mwanafunziinvestor.com/admin')
            ->assertRedirect('https://admin.mwanafunziinvestor.com/admin');
    }

    public function test_admin_host_legacy_login_does_not_loop(): void
    {
        $this->get('https://admin.mwanafunziinvestor.com/admin/login')
            ->assertRedirect('https://admin.mwanafunziinvestor.com/login');
        $response = $this->throughMiddleware('https://admin.mwanafunziinvestor.com/login');
        $this->assertSame(204, $response->getStatusCode());
    }

    public function test_correct_subdomain_login_uses_correct_controller(): void
    {
        $router = app('router');
        $admin = $router->getRoutes()->match(Request::create('https://admin.mwanafunziinvestor.com/login'));
        $client = $router->getRoutes()->match(Request::create('https://client.mwanafunziinvestor.com/login'));
        $this->assertSame(AdminAuthController::class.'@create', $admin->getActionName());
        $this->assertSame(CustomerAuthController::class.'@login', $client->getActionName());
        $this->assertSame(204, $this->throughMiddleware('https://client.mwanafunziinvestor.com/login')->getStatusCode());
    }

    public function test_local_and_staging_hosts_and_post_bodies_are_not_redirected(): void
    {
        foreach (['http://localhost/login', 'https://staging.mwanafunziinvestor.com/login', 'https://unrelated.example/login'] as $url) {
            $this->assertSame(204, $this->throughMiddleware($url)->getStatusCode());
        }
        $this->assertSame(204, $this->throughMiddleware('https://mwanafunziinvestor.com/login', 'POST')->getStatusCode());
        config(['portals.enabled' => false]);
        $this->assertSame(204, $this->throughMiddleware('https://mwanafunziinvestor.com/login')->getStatusCode());
    }

    public function test_entry_link_destinations_are_explicit(): void
    {
        $this->assertSame('https://client.mwanafunziinvestor.com/login', PortalLinks::clientLogin());
        $this->assertSame('https://admin.mwanafunziinvestor.com/login', PortalLinks::adminLogin());
    }

    public function test_google_callback_stays_on_the_client_host_for_oauth_session_compatibility(): void
    {
        $this->assertSame(
            'https://client.mwanafunziinvestor.com/auth/google/callback',
            PortalLinks::googleCallbackFor(Request::create('https://client.mwanafunziinvestor.com/auth/google/redirect')),
        );
        $this->assertNull(PortalLinks::googleCallbackFor(Request::create('https://mwanafunziinvestor.com/auth/google/redirect')));

        config(['portals.enabled' => false]);
        $this->assertNull(PortalLinks::googleCallbackFor(Request::create('https://client.mwanafunziinvestor.com/auth/google/redirect')));
    }

    private function throughMiddleware(string $url, string $method = 'GET'): Response
    {
        return (new RedirectPortalEntryPoints)->handle(Request::create($url, $method), fn () => new Response('', 204));
    }
}
