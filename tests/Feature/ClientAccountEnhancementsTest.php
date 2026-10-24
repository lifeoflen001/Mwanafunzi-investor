<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleReaction;
use App\Models\ClientTwoFactorAuthentication;
use App\Models\User;
use App\Notifications\ClientActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientAccountEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_customer_can_view_saved_published_articles_without_drafts(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);
        $published = Article::create(['title' => 'Saved article', 'slug' => 'saved-article-test', 'content' => '<p>Useful.</p>', 'status' => 'published', 'published_at' => now()->subMinute()]);
        $draft = Article::create(['title' => 'Draft article', 'slug' => 'draft-article-test', 'content' => '<p>Not ready.</p>', 'status' => 'draft']);
        ArticleReaction::create(['user_id' => $customer->id, 'article_id' => $published->id, 'type' => 'like']);
        ArticleReaction::create(['user_id' => $customer->id, 'article_id' => $draft->id, 'type' => 'like']);

        $this->actingAs($customer)->get(route('account.saved'))
            ->assertOk()
            ->assertSee('Saved article')
            ->assertDontSee('Draft article');
    }

    public function test_customer_notifications_are_persisted_owned_and_markable(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);
        $other = User::factory()->create(['is_admin' => false]);
        $customer->notify(new ClientActivityNotification('A private update', 'Only this client should see it.'));
        $other->notify(new ClientActivityNotification('Other private update', 'Must not leak.'));
        $notification = $customer->notifications()->firstOrFail();

        $this->actingAs($customer)->get(route('account.notifications'))
            ->assertOk()
            ->assertSee('A private update')
            ->assertDontSee('Other private update');
        $this->actingAs($customer)->post(route('account.notifications.read', $notification))
            ->assertRedirect(route('account.notifications'));
        $this->assertNotNull($notification->fresh()->read_at);
        $this->actingAs($customer)->post(route('account.notifications.read-all'))->assertSessionHas('success');
    }

    public function test_customer_data_export_contains_account_data_but_never_secrets(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email' => 'export@example.test', 'phone' => '+255700000000', 'admin_mfa_secret' => 'SECRET-MUST-NOT-EXPORT', 'admin_mfa_recovery_codes' => [Hash::make('RECOVERY-1')]]);

        $response = $this->actingAs($customer)->get(route('account.privacy.export'));

        $response->assertOk()->assertHeader('content-type', 'application/json; charset=UTF-8');
        $this->assertStringContainsString('no-store, private', (string) $response->headers->get('cache-control'));
        $body = $response->streamedContent();
        $this->assertStringContainsString('export@example.test', $body);
        $this->assertStringNotContainsString('SECRET-MUST-NOT-EXPORT', $body);
        $this->assertStringNotContainsString('RECOVERY-1', $body);
    }

    public function test_customer_can_revoke_an_owned_other_session_but_not_a_foreign_session(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'password' => 'correct-password']);
        $this->actingAs($customer);
        DB::table('sessions')->insert([
            'id' => 'other-session-test', 'user_id' => $customer->id, 'ip_address' => '127.0.0.2', 'user_agent' => 'Test browser', 'payload' => 'x', 'last_activity' => now()->timestamp,
        ]);

        $this->get(route('account.security'))->assertOk()->assertSee('Signed-in device');
        $this->delete(route('account.security.sessions.revoke', 'other-session-test'))->assertSessionHas('success');
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session-test']);
        DB::table('sessions')->insert(['id' => 'foreign-session-test', 'user_id' => User::factory()->create(['is_admin' => false])->id, 'ip_address' => '127.0.0.3', 'user_agent' => 'Other browser', 'payload' => 'x', 'last_activity' => now()->timestamp]);
        $this->delete(route('account.security.sessions.revoke', 'foreign-session-test'))->assertSessionHas('success');
        $this->assertDatabaseHas('sessions', ['id' => 'foreign-session-test']);
    }

    public function test_enabled_client_mfa_requires_a_recovery_code_before_workspace_access(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'email_verified_at' => now(), 'password' => 'correct-password']);
        ClientTwoFactorAuthentication::create(['user_id' => $customer->id, 'secret' => 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', 'recovery_codes' => [Hash::make('RECOVERY-1')], 'confirmed_at' => now()]);

        $this->actingAs($customer)->get(route('account.dashboard'))
            ->assertRedirect(route('account.security.challenge'));
        $this->actingAs($customer)->post(route('account.security.challenge.verify'), ['code' => 'RECOVERY-1'])
            ->assertRedirect(route('account.dashboard'));
        $this->assertCount(0, $customer->fresh()->clientTwoFactorAuthentication->recovery_codes);
        $this->actingAs($customer)->get(route('account.dashboard'))->assertOk();
    }

    public function test_customer_can_open_client_mfa_setup_without_exposing_an_existing_secret(): void
    {
        $customer = User::factory()->create(['is_admin' => false, 'password' => 'correct-password']);

        $this->actingAs($customer)->get(route('account.security.2fa.setup'))
            ->assertOk()
            ->assertSee('Connect your authenticator.')
            ->assertSee('otpauth://totp/', false)
            ->assertDontSee('admin_mfa_secret');
    }
}
