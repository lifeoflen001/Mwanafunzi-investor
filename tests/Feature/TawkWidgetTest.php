<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TawkWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_tawk_widget_is_available_on_public_pages_with_a_csp_nonce(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('https://embed.tawk.to/6ad8c28aa03d3a34b7a7c606/1k5fbfsa8', false);

        $nonce = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($nonce);
        preg_match("/script-src[^;]*'nonce-([^']+)'/", $nonce, $matches);

        $this->assertNotEmpty($matches[1] ?? null);
        $response->assertSee('nonce="'.$matches[1].'"', false);
    }

    public function test_tawk_widget_is_not_loaded_on_admin_login(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertDontSee('embed.tawk.to', false);
    }
}
