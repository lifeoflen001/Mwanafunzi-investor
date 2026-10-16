<?php

namespace Tests\Feature;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.google.client_id', 'test-client-id');
        config()->set('services.google.client_secret', 'test-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');
    }

    public function test_google_sign_in_creates_a_client_and_returns_to_the_public_site(): void
    {
        Socialite::fake('google', $this->googleUser());

        $response = $this->withSession([
            'google.auth.intent' => 'login',
            'auth.redirect' => '/',
        ])->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google-client@example.com',
            'is_admin' => false,
            'password' => null,
        ]);
        $this->assertDatabaseHas('social_identities', [
            'provider' => 'google',
            'provider_user_id' => 'google-subject-1',
            'provider_email' => 'google-client@example.com',
        ]);
    }

    public function test_google_sign_in_uses_the_linked_identity_and_keeps_a_safe_return_path(): void
    {
        $client = User::factory()->create([
            'email' => 'linked@example.com',
            'is_admin' => false,
            'status' => 'active',
        ]);
        SocialIdentity::create([
            'user_id' => $client->id,
            'provider' => 'google',
            'provider_user_id' => 'google-subject-1',
            'provider_email' => $client->email,
        ]);
        Socialite::fake('google', $this->googleUser(['email' => $client->email]));

        $response = $this->withSession([
            'google.auth.intent' => 'login',
            'auth.redirect' => '/account/orders',
        ])->get(route('auth.google.callback'));

        $response->assertRedirect('/account/orders');
        $this->assertAuthenticatedAs($client);
    }

    public function test_google_does_not_authenticate_an_administrator(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
            'status' => 'active',
        ]);
        Socialite::fake('google', $this->googleUser(['email' => $admin->email]));

        $response = $this->withSession([
            'google.auth.intent' => 'login',
            'auth.redirect' => '/',
        ])->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertDatabaseMissing('social_identities', ['user_id' => $admin->id]);
    }

    public function test_same_email_google_sign_in_requires_password_confirmation_before_linking(): void
    {
        $client = User::factory()->create([
            'email' => 'existing@example.com',
            'is_admin' => false,
            'status' => 'active',
        ]);
        Socialite::fake('google', $this->googleUser(['email' => $client->email]));

        $googleResponse = $this->withSession([
            'google.auth.intent' => 'login',
            'auth.redirect' => '/account/profile',
        ])->get(route('auth.google.callback'));

        $googleResponse->assertRedirect(route('login'));
        $googleResponse->assertSessionHas('google.pending_identity');
        $this->assertGuest();
        $this->assertDatabaseMissing('social_identities', ['user_id' => $client->id]);

        $loginResponse = $this->post(route('login.store'), [
            'email' => $client->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect('/account/profile');
        $this->assertAuthenticatedAs($client);
        $this->assertDatabaseHas('social_identities', [
            'user_id' => $client->id,
            'provider' => 'google',
            'provider_user_id' => 'google-subject-1',
        ]);
    }

    public function test_external_google_return_paths_are_replaced_with_the_public_default(): void
    {
        Socialite::fake('google', $this->googleUser());

        $response = $this->withSession([
            'google.auth.intent' => 'login',
            'auth.redirect' => 'https://evil.example/steal-session',
        ])->get(route('auth.google.callback'));

        $response->assertRedirect('/');
    }

    public function test_google_only_clients_must_set_a_password_before_disconnect(): void
    {
        $client = User::factory()->create([
            'password' => null,
            'is_admin' => false,
            'status' => 'active',
        ]);
        SocialIdentity::create([
            'user_id' => $client->id,
            'provider' => 'google',
            'provider_user_id' => 'google-subject-1',
            'provider_email' => $client->email,
        ]);

        $response = $this->actingAs($client)->post(route('account.security.google.disconnect'));

        $response->assertSessionHasErrors('google');
        $this->assertDatabaseHas('social_identities', ['user_id' => $client->id]);
        $this->assertAuthenticatedAs($client);
    }

    private function googleUser(array $overrides = []): GoogleUser
    {
        return GoogleUser::fake(array_merge([
            'id' => 'google-subject-1',
            'name' => 'Google Client',
            'email' => 'google-client@example.com',
            'avatar' => 'https://example.com/google-client.jpg',
            'email_verified' => true,
        ], $overrides));
    }
}
