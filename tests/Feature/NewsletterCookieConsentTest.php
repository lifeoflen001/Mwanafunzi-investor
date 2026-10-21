<?php

namespace Tests\Feature;

use App\Mail\NewsletterConfirmationMail;
use App\Models\CookieRegistry;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Support\CookieConsentSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterCookieConsentTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_footer_newsletter_signup_requires_confirmation_and_can_be_confirmed(): void
    {
        Mail::fake();

        $this->post(route('newsletter.subscribe'), ['email' => 'Student@Example.com'])->assertRedirect()->assertSessionHas('newsletter_status', 'check-inbox');
        $subscriber = NewsletterSubscriber::firstOrFail();
        $this->assertSame(NewsletterSubscriber::PENDING, $subscriber->status);
        Mail::assertQueued(NewsletterConfirmationMail::class);

        $token = 'confirmation-token';
        $subscriber->update(['confirmation_token_hash' => hash('sha256', $token)]);
        $this->get(URL::temporarySignedRoute('newsletter.confirm', now()->addMinutes(10), ['token' => $token]))->assertOk()->assertSee("You're subscribed.");
        $this->assertSame(NewsletterSubscriber::ACTIVE, $subscriber->fresh()->status);
    }

    public function test_duplicate_and_unsubscribe_flows_are_controlled(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'active@example.com', 'status' => NewsletterSubscriber::ACTIVE, 'source' => 'footer', 'confirmed_at' => now(),
            'unsubscribe_token_hash' => hash('sha256', 'unsubscribe-token'), 'unsubscribe_token' => 'unsubscribe-token',
        ]);

        $this->post(route('newsletter.subscribe'), ['email' => 'ACTIVE@example.com'])->assertRedirect()->assertSessionHas('newsletter_status', 'already');
        $this->get(URL::signedRoute('newsletter.unsubscribe', ['token' => 'unsubscribe-token']))->assertOk()->assertSee("You're unsubscribed.", false);
        $this->assertSame(NewsletterSubscriber::UNSUBSCRIBED, $subscriber->fresh()->status);
    }

    public function test_confirmed_newsletter_keeps_a_usable_unsubscribe_token(): void
    {
        Mail::fake();

        $this->post(route('newsletter.subscribe'), ['email' => 'token@example.test'])
            ->assertRedirect();

        $subscriber = NewsletterSubscriber::firstOrFail();
        $token = 'confirmed-token';
        $subscriber->forceFill(['confirmation_token_hash' => hash('sha256', $token)])->save();

        $this->get(URL::temporarySignedRoute('newsletter.confirm', now()->addMinutes(10), ['token' => $token]))->assertOk();

        $this->assertNotEmpty($subscriber->fresh()->unsubscribe_token);
        Mail::assertQueued(\App\Mail\NewsletterConfirmedMail::class);
    }

    public function test_admin_newsletter_and_privacy_surfaces_are_authorized_and_paginated(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);
        NewsletterSubscriber::create(['email' => 'first@example.com', 'status' => NewsletterSubscriber::PENDING, 'source' => 'footer']);
        NewsletterSubscriber::create(['email' => 'second@example.com', 'status' => NewsletterSubscriber::ACTIVE, 'source' => 'blog', 'confirmed_at' => now()]);

        $this->actingAs($user)->get(route('admin.newsletter'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.newsletter'))->assertOk()->assertSee('Newsletter subscribers');
        $this->actingAs($admin)->get(route('admin.settings.cookies'))->assertOk()->assertSee('Cookie consent');
        $this->actingAs($admin)->get(route('admin.settings.cookies.registry'))->assertOk()->assertSee('Cookie and service registry');
        $this->assertNotEmpty(CookieRegistry::all());
    }

    public function test_public_cookie_policy_and_footer_controls_render(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('data-cookie-consent', false)->assertSee('data-newsletter-form', false)->assertSee('footer-contact-newsletter', false)->assertSee('Cookie Preferences')->assertSee('Refund Policy')->assertSee('Cookie Policy');
        $this->get(route('cookie-policy'))->assertOk()->assertSee('Cookie policy')->assertSee('laravel_session');
        $this->assertTrue(CookieConsentSettings::all()['enabled']);
    }

    public function test_cookie_preferences_render_all_enabled_categories_with_switch_semantics(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.settings.cookies.update'), [
            'enabled' => 1, 'banner_heading' => 'Cookie choices', 'banner_description' => 'Choose your optional categories.', 'accept_all_label' => 'Accept all', 'reject_optional_label' => 'Reject optional', 'manage_preferences_label' => 'Manage preferences', 'modal_heading' => 'Cookie Preferences',
            'lifetime_months' => 12, 'version' => '2.0', 'policy_url' => '/cookie-policy', 'privacy_url' => '/privacy-policy',
            'essential_description' => 'Essential.', 'preferences_description' => 'Preferences.', 'analytics_description' => 'Analytics.', 'marketing_description' => 'Marketing.', 'external_media_description' => 'External media.',
            'analytics_enabled' => 1, 'marketing_enabled' => 1, 'external_media_enabled' => 1,
        ])->assertRedirect();

        $this->get(route('home'))->assertOk()
            ->assertSee('Necessary Cookies')
            ->assertSee('Analytics Cookies')
            ->assertSee('Marketing Cookies')
            ->assertSee('Preference Cookies')
            ->assertSee('data-cookie-toggle="preferences"', false)
            ->assertSee('data-cookie-toggle="analytics"', false)
            ->assertSee('data-cookie-toggle="marketing"', false)
            ->assertSee('data-cookie-toggle="external_media"', false)
            ->assertSee('role="switch"', false)
            ->assertSee('Always Active');
    }

    public function test_admin_copy_changes_reach_public_surfaces_without_manual_cache_clearing(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.settings.newsletter.update'), [
            'enabled' => 1, 'footer_visible' => 1, 'confirmation_required' => 1,
            'heading' => 'Desk updates', 'description' => 'Fresh notes from the desk.', 'success_message' => 'Check your inbox.', 'consent_copy' => 'Email updates only.', 'privacy_url' => '/privacy-policy',
            'sender_name' => 'Mwanafunzi Investor', 'sender_email' => 'mwanafunziinvestor@outlook.com', 'reply_to_email' => 'mwanafunziinvestor@outlook.com',
        ])->assertRedirect();
        $this->get(route('home'))->assertSee('Desk updates')->assertSee('Fresh notes from the desk.');

        $this->actingAs($admin)->put(route('admin.settings.cookies.update'), [
            'enabled' => 1, 'banner_heading' => 'Updated cookie choices', 'banner_description' => 'Updated privacy copy.', 'accept_all_label' => 'Allow all', 'reject_optional_label' => 'Essential only', 'manage_preferences_label' => 'Choose cookies', 'modal_heading' => 'Choose cookies',
            'lifetime_months' => 12, 'version' => '2.0', 'policy_url' => '/cookie-policy', 'privacy_url' => '/privacy-policy',
            'essential_description' => 'Essential only.', 'preferences_description' => 'Preferences.', 'analytics_description' => 'Analytics.', 'marketing_description' => 'Marketing.', 'external_media_description' => 'External media.',
        ])->assertRedirect();
        $this->get(route('home'))->assertSee('Updated cookie choices')->assertSee('Essential only');
    }
}
