<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClientAuthenticationExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_normal_login_defaults_to_the_client_dashboard(): void
    {
        $customer = User::factory()->create([
            'name' => 'Ms. Asha Mollel',
            'email' => 'asha@example.test',
            'password' => 'password-12345',
            'is_admin' => false,
            'status' => 'active',
        ]);

        $this->get(route('login'))->assertOk()->assertSee('name="email"', false);

        $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password-12345',
        ])->assertRedirect(route('account.dashboard'));

        $this->assertAuthenticatedAs($customer);
        $this->assertSame('Asha', $customer->fresh()->firstName());
    }

    public function test_registration_defaults_to_the_client_dashboard_and_sends_verification(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Dr. Neema Joseph',
            'email' => 'neema@example.test',
            'password' => 'password-12345',
            'password_confirmation' => 'password-12345',
            'terms' => '1',
        ])->assertRedirect(route('account.dashboard'));

        $customer = User::where('email', 'neema@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($customer);
        $this->assertSame('Neema', $customer->firstName());
        Notification::assertSentTo($customer, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_guest_comment_intent_and_draft_survive_login_and_submission(): void
    {
        $category = ArticleCategory::where('is_active', true)->firstOrFail();
        $article = Article::create([
            'article_category_id' => $category->id,
            'title' => 'A deliberate process note',
            'slug' => 'a-deliberate-process-note',
            'excerpt' => 'A useful note for the journal.',
            'content' => '<p>Process before reaction.</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $customer = User::factory()->create([
            'email' => 'reader@example.test',
            'password' => 'password-12345',
            'is_admin' => false,
            'status' => 'active',
        ]);

        $response = $this->post(route('blog.comments.store', $article), [
            'body' => 'This draft should survive authentication.',
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString(route('login'), $response->headers->get('Location'));
        $this->get($response->headers->get('Location'))->assertOk();

        $this->actingAs($customer)
            ->get(route('blog.show', $article))
            ->assertOk()
            ->assertSee('This draft should survive authentication.')
            ->assertSee('Your comment draft was restored.');

        $this->actingAs($customer)->post(route('blog.comments.store', $article), [
            'body' => 'This draft should survive authentication.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $customer->id,
            'body' => 'This draft should survive authentication.',
            'status' => 'pending',
        ]);
        $this->assertFalse(session()->has('auth_comment_draft'));
    }

    public function test_guest_reaction_and_checkout_intents_return_to_the_requested_context(): void
    {
        $category = ArticleCategory::where('is_active', true)->firstOrFail();
        $article = Article::create([
            'article_category_id' => $category->id,
            'title' => 'A saved note',
            'slug' => 'a-saved-note',
            'content' => '<p>Save the context.</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);

        $reaction = $this->post(route('blog.reaction', $article), ['type' => 'like']);
        $reaction->assertRedirect();
        $this->assertStringContainsString(route('login'), $reaction->headers->get('Location'));
        $this->assertStringContainsString('/blog/'.$article->slug, urldecode($reaction->headers->get('Location')));

        $course = \App\Models\Course::firstOrFail();
        $checkout = $this->get(route('checkout.course', $course));
        $checkout->assertRedirect();
        $this->assertStringContainsString(route('login'), $checkout->headers->get('Location'));
        $this->assertStringContainsString('/checkout/course/'.$course->slug, urldecode($checkout->headers->get('Location')));
    }

    public function test_external_login_redirect_is_rejected_and_account_menu_uses_post_logout(): void
    {
        $customer = User::factory()->create([
            'name' => 'Asha Mollel',
            'email' => 'menu@example.test',
            'password' => 'password-12345',
            'is_admin' => false,
            'status' => 'active',
        ]);

        $this->get(route('login', ['redirect' => 'https://evil.example/steal']))
            ->assertOk()
            ->assertSee('name="redirect"', false);

        $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password-12345',
            'redirect' => 'https://evil.example/steal',
        ])->assertRedirect(route('account.dashboard'));

        $this->actingAs($customer)
            ->get('/')
            ->assertOk()
            ->assertSee('My Dashboard')
            ->assertSee('My learning')
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('method="post"', false);
    }
}
