<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SiteSetting;
use App\Services\RecaptchaService;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_totp_matches_known_vector(): void
    {
        $this->assertTrue(Totp::verify('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', '287082', 59));
        $this->assertFalse(Totp::verify('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', '000000', 59));
    }

    public function test_production_admin_access_requires_mfa_setup(): void
    {
        config()->set('security.admin_mfa_required', true);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertRedirect(route('admin.mfa.setup'));
        $this->actingAs($admin)->get(route('admin.mfa.setup'))->assertOk()->assertSee('Protect the admin desk.');
    }

    public function test_enabled_admin_must_complete_challenge_and_recovery_code_is_one_time(): void
    {
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_mfa_enabled' => true,
            'admin_mfa_secret' => $secret,
            'admin_mfa_recovery_codes' => [Hash::make('ABCD-1234')],
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertRedirect(route('admin.mfa.challenge'));
        $this->post(route('admin.mfa.challenge.verify'), ['code' => 'ABCD-1234'])->assertRedirect(route('admin.dashboard'));
        $this->assertSame(0, count((array) $admin->fresh()->admin_mfa_recovery_codes));
        $this->post(route('admin.mfa.challenge.verify'), ['code' => 'ABCD-1234'])->assertSessionHasErrors('code');
    }

    public function test_login_failure_message_does_not_disclose_account_existence(): void
    {
        $customer = User::factory()->create(['email' => 'known@example.com', 'is_admin' => false]);

        $known = $this->from(route('login'))->post(route('login.store'), ['email' => $customer->email, 'password' => 'wrong-password']);
        $unknown = $this->from(route('login'))->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'wrong-password']);

        $known->assertRedirect(route('login'))->assertSessionHasErrors(['email' => 'The email or password is incorrect.']);
        $unknown->assertRedirect(route('login'))->assertSessionHasErrors(['email' => 'The email or password is incorrect.']);
    }

    public function test_admin_redirect_validation_rejects_external_protocol_relative_targets(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.redirects.store'), [
            'source_path' => '/safe-path',
            'destination_path' => '//evil.example/login',
            'status_code' => 302,
        ])->assertStatus(422);
        $this->actingAs($admin)->post(route('admin.redirects.store'), [
            'source_path' => '/safe-script',
            'destination_path' => 'javascript:alert(1)',
            'status_code' => 302,
        ])->assertStatus(422);
    }

    public function test_mfa_verification_is_rate_limited_and_disable_requires_reauthentication(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_mfa_enabled' => true,
            'admin_mfa_secret' => 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->actingAs($admin)->post(route('admin.mfa.challenge.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        }
        $this->actingAs($admin)->post(route('admin.mfa.challenge.verify'), ['code' => '000000'])->assertStatus(429);

        $secondAdmin = User::factory()->create([
            'is_admin' => true,
            'admin_mfa_enabled' => true,
            'admin_mfa_secret' => 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
        ]);
        $this->withSession(['admin_mfa_verified_user_id' => $secondAdmin->id])
            ->actingAs($secondAdmin)
            ->post(route('admin.profile.mfa.disable'), ['current_password' => 'wrong-password', 'code' => '000000'])
            ->assertSessionHasErrors('current_password');
        $this->assertTrue((bool) $secondAdmin->fresh()->admin_mfa_enabled);
    }

    public function test_security_headers_are_present_and_private_disk_is_not_served_by_framework_route(): void
    {
        $response = $this->get(route('health'));
        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy');

        $this->get('/storage/commerce/private.pdf')->assertNotFound();
    }

    public function test_recaptcha_is_disabled_in_testing_and_does_not_call_google(): void
    {
        config()->set('recaptcha.enabled', true);
        Http::fake();

        $service = app(RecaptchaService::class);

        $this->assertFalse($service->isEnabled('login'));
        $this->assertTrue($service->verify(null, 'login', request()));
        Http::assertNothingSent();
    }

    public function test_enabled_recaptcha_verifies_action_score_and_hostname_server_side(): void
    {
        config()->set([
            'recaptcha.enforce_in_testing' => true,
            'recaptcha.enabled' => true,
            'recaptcha.site_key' => 'site-key',
            'recaptcha.secret_key' => 'secret-key',
            'recaptcha.min_score' => 0.7,
            'recaptcha.hostname' => 'example.test',
        ]);
        Http::fake(['https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'action' => 'login', 'score' => 0.9, 'hostname' => 'example.test'])]);

        $this->assertTrue(app(RecaptchaService::class)->verify('token', 'login', request()));
        Http::assertSent(fn ($request) => $request->url() === 'https://www.google.com/recaptcha/api/siteverify' && $request['secret'] === 'secret-key' && $request['response'] === 'token');
    }

    public function test_enabled_recaptcha_rejects_missing_token_before_login_attempt(): void
    {
        config()->set(['recaptcha.enforce_in_testing' => true, 'recaptcha.enabled' => true, 'recaptcha.site_key' => 'site-key', 'recaptcha.secret_key' => 'secret-key']);
        Http::fake();

        $this->from(route('login'))->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'wrong-password'])
            ->assertRedirect(route('login'))->assertSessionHasErrors('recaptcha_token');
        Http::assertNothingSent();
    }

    public function test_admin_security_panel_encrypts_recaptcha_secret_and_is_admin_only(): void
    {
        config()->set('security.admin_mfa_required', false);
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)->get(route('admin.settings.security'))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->put(route('admin.settings.security.update'), [
            'recaptcha_enabled' => '1',
            'recaptcha_site_key' => 'site-key',
            'recaptcha_secret_key' => 'secret-key',
            'recaptcha_min_score' => '0.65',
            'recaptcha_forms' => ['login' => '1', 'contact' => '1'],
        ])->assertRedirect();

        $setting = SiteSetting::where('key', 'security.recaptcha.secret_key')->firstOrFail();
        $this->assertSame('encrypted', $setting->type);
        $this->assertNotSame('secret-key', $setting->value);
        $this->assertSame('secret-key', Crypt::decryptString($setting->value));
    }
}
