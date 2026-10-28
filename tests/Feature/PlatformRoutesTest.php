<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\Course;
use App\Models\Product;
use App\Models\Media;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\Page;
use App\Models\NavigationItem;
use App\Models\LearningTopic;
use App\Models\User;
use App\Models\AdminAuditLog;
use App\Models\TeamMember;
use App\Models\SocialLink;
use App\Models\ArticleSocialLink;
use App\Models\Comment;
use App\Models\Tag;
use App\Notifications\AdminResetPasswordNotification;
use App\Support\PublicSiteData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PlatformRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_public_platform_routes_render(): void
    {
        foreach (['/', '/development', '/studio', '/financial-academy', '/financial-academy/learn', '/financial-academy/learn/forex-core-basics', '/financial-academy/courses', '/financial-academy/tools', '/journal', '/about', '/about/team/lenkai-mollel', '/about/team/david-lyengi', '/contact', '/student-of-money', '/risk-disclosure', '/privacy-policy', '/terms', '/refund-policy', '/disclaimer', '/sitemap.xml'] as $route) {
            $this->get($route)->assertSuccessful();
        }

        $this->get('/about')->assertDontSee('\\n\\n')->assertSee('Lenkai Mollel')->assertSee('David Lyengi');
        $this->get('/about/team/lenkai-mollel')->assertSee('Lenkai Mollel')->assertSee('Meet the team')->assertDontSee('Meet more of the team.');
        $this->get('/about/team/david-lyengi')->assertSee('David Lyengi')->assertSee('Meet the team');
        $this->get('/courses')->assertRedirect('/financial-academy/courses');
        $this->get('/forex-academy')->assertRedirect('/financial-academy');
    }

    public function test_policy_pages_have_specific_document_titles(): void
    {
        $this->get('/privacy-policy')->assertSee('<title>Privacy Policy — Mwanafunzi Investor</title>', false);
        $this->get('/terms')->assertSee('<title>Terms of Use — Mwanafunzi Investor</title>', false);
        $this->get('/disclaimer')->assertSee('<title>Educational Disclaimer — Mwanafunzi Investor</title>', false);
    }

    public function test_active_administrators_can_open_client_account_routes(): void
    {
        $administrator = User::factory()->create(['is_admin' => true, 'status' => 'active']);

        $this->actingAs($administrator)->get(route('account.dashboard'))->assertOk()->assertSee('Student portal');
        $this->actingAs($administrator)->get(route('account.orders'))->assertOk()->assertSee('Student portal');

        $suspendedAdministrator = User::factory()->create(['is_admin' => true, 'status' => 'suspended']);
        $this->actingAs($suspendedAdministrator)->get(route('account.dashboard'))->assertForbidden();
    }

    public function test_service_sites_keep_scoped_navigation_and_shared_footer(): void
    {
        foreach (['/digital-software', '/digital-software/products', '/digital-software/projects', '/digital-software/testimonials', '/creative-studio', '/creative-studio/services', '/creative-studio/projects', '/creative-studio/testimonials'] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('site-footer', false)
                ->assertSee('footer-company', false)
                ->assertSee('Cookie Preferences')
                ->assertDontSee('digital-software-footer', false);
        }

        $this->get('/digital-software')
            ->assertSee('/digital-software/products')
            ->assertSee('Digital Software')
            ->assertSee('Contact us')
            ->assertDontSee('service-nav-label', false)
            ->assertDontSee('service-testimonials', false)
            ->assertDontSee('Client perspectives are published')
            ->assertDontSee('Start a conversation');
        $this->get('/creative-studio')
            ->assertSee('/creative-studio/projects')
            ->assertSee('Creative Studio')
            ->assertSee('Contact us')
            ->assertDontSee('service-nav-label', false)
            ->assertDontSee('Start a conversation');
    }

    public function test_client_login_surface_uses_accessible_auth_controls(): void
    {
        $login = $this->get(route('login'))
            ->assertOk()
            ->assertSee('Client portal')
            ->assertSee('client-password', false)
            ->assertSee('data-password-toggle', false)
            ->assertSee('auth-shell', false)
            ->assertSee('client-auth-layout', false)
            ->assertSee('client-auth-panel', false)
            ->assertSee('client-auth-showcase', false)
            ->assertSee('Create an account')
            ->assertSee('Back to website')
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false)
            ->assertDontSee('data-auth-slide', false)
            ->assertSee('Forgot password')
            ->assertSeeInOrder(['id="client-email"', 'id="client-password"', 'data-auth-submit', '<span>or</span>', 'Continue with Google'], false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your account.')
            ->assertSee('client-password-confirmation', false)
            ->assertSee('data-password-toggle', false)
            ->assertSee('auth-shell', false)
            ->assertSee('client-auth-layout', false)
            ->assertSee('client-auth-panel', false)
            ->assertSee('client-auth-showcase', false)
            ->assertSee('I accept the')
            ->assertSee('Back to website')
            ->assertDontSee('site-header', false)
            ->assertDontSee('site-footer', false)
            ->assertDontSee('data-auth-slide', false)
            ->assertDontSee('client-phone', false)
            ->assertDontSee('client-country', false)
            ->assertSeeInOrder(['id="client-name"', 'id="client-email"', 'id="client-password"', 'data-auth-submit', '<span>or</span>', 'Continue with Google'], false);

        $this->post(route('login.store'), ['email' => 'not-an-email', 'password' => ''])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_inline_form_errors_are_not_repeated_in_the_summary(): void
    {
        $user = User::factory()->create([
            'email' => 'form-errors@example.test',
            'password' => Hash::make('correct-password'),
            'is_admin' => false,
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $page = $this->get(route('login'));

        $page->assertOk()
            ->assertDontSee('Review the form errors below.')
            ->assertDontSee('Please correct the highlighted field.')
            ->assertSee('The email or password is incorrect.');

        $this->assertSame(1, substr_count($page->getContent(), 'The email or password is incorrect.'));
    }

    public function test_admin_login_exposes_a_separate_admin_password_reset_flow(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'email' => 'admin-reset@example.com',
            'is_admin' => true,
            'status' => 'active',
        ]);
        $customer = User::factory()->create([
            'email' => 'customer-reset@example.com',
            'is_admin' => false,
            'status' => 'active',
        ]);

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Forgot password?');

        $this->get(route('admin.password.request'))
            ->assertOk()
            ->assertSee('Reset administrator access.')
            ->assertSee('Send reset link');

        $this->post(route('admin.password.email'), ['email' => $admin->email])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class);
        Notification::assertNotSentTo($customer, AdminResetPasswordNotification::class);

        $this->get(route('admin.password.reset', ['token' => 'test-token']))
            ->assertOk()
            ->assertSee('Choose a new password.');
    }

    public function test_admin_password_reset_updates_an_admin_and_rejects_client_tokens(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'active']);
        $customer = User::factory()->create(['is_admin' => false, 'status' => 'active']);

        $adminToken = Password::broker()->createToken($admin);

        $this->post(route('admin.password.update'), [
            'token' => $adminToken,
            'email' => $admin->email,
            'password' => 'new-admin-password',
            'password_confirmation' => 'new-admin-password',
        ])->assertRedirect(route('admin.login'));

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Your administrator password has been reset. Sign in with the new password.');

        $this->assertTrue(Hash::check('new-admin-password', $admin->fresh()->password));

        $customerToken = Password::broker()->createToken($customer);

        $this->post(route('admin.password.update'), [
            'token' => $customerToken,
            'email' => $customer->email,
            'password' => 'should-not-be-applied',
            'password_confirmation' => 'should-not-be-applied',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $customer->fresh()->password));
    }

    public function test_health_endpoint_returns_only_public_status(): void
    {
        $this->get('/health')->assertOk()->assertExactJson(['status' => 'ok']);
    }

    public function test_robots_uses_the_application_sitemap_url(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_business_module_content_propagates_from_pages_cms(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::where('key', 'development')->firstOrFail();
        $capabilities = $page->sections()->where('key', 'capabilities')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.pages.edit', $page))->assertOk()->assertSee('Cards');
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'strong', 'hero_alignment' => 'left',
            'sections' => [$capabilities->id => [
                'heading' => 'Systems managed from the desk.', 'body' => 'CMS-managed module copy.', 'sort_order' => 10, 'is_enabled' => 1,
                'cards' => 'Web apps | A CMS-managed service card. | Laravel · Workflows',
            ]],
        ])->assertRedirect();

        $this->get('/development')->assertOk()->assertSee('Systems managed from the desk.')->assertSee('A CMS-managed service card.');
        $digitalResponse = $this->get(route('digital-systems'));
        $digitalResponse->assertOk()
            ->assertSee('Systems managed from the desk.')
            ->assertSee('A CMS-managed service card.');
        $this->assertStringContainsString('no-cache', (string) $digitalResponse->headers->get('Cache-Control'));
        $this->assertStringContainsString('must-revalidate', (string) $digitalResponse->headers->get('Cache-Control'));
    }

    public function test_cms_section_visibility_and_explicit_empty_values_persist_publicly(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::where('key', 'development')->firstOrFail();
        $hospitality = $page->sections()->where('key', 'hospitality')->firstOrFail();
        $selectedWork = $page->sections()->where('key', 'selected-work')->firstOrFail();

        $base = [
            'name' => $page->name,
            'key' => $page->key,
            'slug' => $page->slug,
            'page_type' => $page->page_type,
            'status' => 'published',
            'is_visible' => 1,
            'hero_focal_point' => $page->hero_focal_point,
            'hero_overlay' => $page->hero_overlay,
            'hero_alignment' => $page->hero_alignment,
        ];

        $this->actingAs($admin)->put(route('admin.pages.update', $page), $base + [
            'sections' => [
                $hospitality->id => ['sort_order' => $hospitality->sort_order, 'is_enabled' => 0],
                $selectedWork->id => ['sort_order' => $selectedWork->sort_order, 'is_enabled' => 1, 'body' => ''],
            ],
        ])->assertRedirect();

        $hidden = $this->get(route('digital-systems'))->assertOk();
        $hidden->assertDontSee('digital-hospitality-section', false)
            ->assertDontSee('Websites and operational systems for hospitality businesses.')
            ->assertDontSee('Systems and digital products designed around real workflows, operational needs and business problems.');

        $this->actingAs($admin)->put(route('admin.pages.update', $page), $base + [
            'sections' => [
                $hospitality->id => [
                    'sort_order' => $hospitality->sort_order,
                    'is_enabled' => 1,
                    'heading' => 'Verified hospitality systems',
                    'body' => 'CMS restored content.',
                ],
            ],
        ])->assertRedirect();

        $visible = $this->get(route('digital-systems'))->assertOk();
        $visible->assertSee('digital-hospitality-section', false)
            ->assertSee('Verified hospitality systems')
            ->assertSee('CMS restored content.');
        $this->assertSame('', (string) $selectedWork->fresh()->body);
    }

    public function test_about_story_and_mission_vision_are_editable_from_page_cms(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::where('key', 'about')->firstOrFail();
        $story = $page->sections()->where('key', 'philosophy')->firstOrFail();
        $missionVision = $page->sections()->where('key', 'mission-vision')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type,
            'status' => 'published', 'is_visible' => 1, 'hero_overlay' => 'medium', 'hero_alignment' => 'left',
            'sections' => [
                $story->id => [
                    'eyebrow' => 'Why this exists', 'heading' => 'A different kind of market education.', 'sort_order' => 10, 'is_enabled' => 1,
                    'image' => 'images/hero-about.webp', 'image_alt' => 'A managed research desk.', 'image_focal_point' => 'center 35%',
                    'who_heading' => 'Who we are', 'who_body' => 'CMS-managed Who We Are copy.', 'story_heading' => 'Our story', 'story_body' => 'CMS-managed story copy.',
                    'quote_label' => 'Our philosophy', 'quote' => 'A managed philosophy quote.',
                ],
                $missionVision->id => [
                    'heading' => $missionVision->heading, 'sort_order' => 15, 'is_enabled' => 1,
                    'mission_label' => 'Our mission', 'mission_heading' => 'A managed mission heading.', 'mission_body' => 'A managed mission body.', 'mission_enabled' => 1,
                    'vision_label' => 'Our vision', 'vision_heading' => 'A managed vision heading.', 'vision_body' => 'A managed vision body.', 'vision_enabled' => 1,
                ],
            ],
        ])->assertRedirect();

        $this->get(route('about'))->assertOk()->assertSee('CMS-managed Who We Are copy.')->assertSee('CMS-managed story copy.')->assertSee('A managed mission heading.')->assertSee('A managed vision heading.')->assertSee('hero-about.webp');
    }

    public function test_visible_social_links_render_as_accessible_footer_icons(): void
    {
        SocialLink::create(['label' => 'Instagram', 'url' => 'https://instagram.com/mwanafunziinvestor', 'sort_order' => 1, 'is_visible' => true]);
        PublicSiteData::forgetCache();

        $this->get(route('about'))->assertOk()->assertSee('aria-label="Visit our Instagram"', false)->assertSee('<svg viewBox="0 0 24 24"', false);
    }

    public function test_visible_social_links_render_when_newsletter_footer_is_hidden(): void
    {
        SocialLink::create(['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/mwanafunzi', 'sort_order' => 1, 'is_visible' => true]);
        SiteSetting::updateOrCreate(['key' => 'newsletter.footer_visible'], ['value' => '0', 'type' => 'boolean']);
        SiteSetting::forgetCache();
        PublicSiteData::forgetCache();

        $this->get(route('about'))->assertOk()->assertSee('aria-label="Visit our LinkedIn"', false)->assertSee('<svg viewBox="0 0 24 24"', false);
    }

    public function test_published_and_coming_soon_content_resolves_by_slug(): void
    {
        $this->get('/financial-academy/courses/forex-foundations')->assertSuccessful()->assertSee('Forex Foundations')->assertSee('application/ld+json')->assertSee('canonical');
        $this->get('/financial-academy/tools/trading-journal-sheet')->assertSuccessful()->assertSee('Trading Journal Sheet')->assertSee('application/ld+json');
    }

    public function test_journal_discovery_social_links_and_feed_use_real_articles(): void
    {
        $member = TeamMember::where('slug', 'lenkai-mollel')->firstOrFail();
        $category = ArticleCategory::firstOrCreate(['slug' => 'risk-management'], ['name' => 'Risk Management']);
        $article = Article::create([
            'article_category_id' => $category->id,
            'team_member_id' => $member->id,
            'title' => 'Why risk belongs before entries',
            'slug' => 'why-risk-belongs-before-entries',
            'excerpt' => 'A practical note on process and risk.',
            'content' => '<p>Risk is part of the process. #InternalTag</p><h2>Build the plan</h2><p>Use a repeatable framework before taking action.</p><h3>Start with risk</h3><p>Define the downside before the entry.</p>',
            'content_type' => 'risk_management',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'is_featured' => true,
        ]);
        $tag = Tag::create(['name' => 'Position Sizing', 'slug' => 'position-sizing']);
        $article->tags()->attach($tag);
        ArticleSocialLink::create(['article_id' => $article->id, 'platform' => 'instagram', 'url' => 'https://instagram.com/p/example', 'label' => 'Watch the original update']);

        $journalResponse = $this->get(route('journal'));
        $journalResponse->assertOk()
            ->assertSee('Why risk belongs before entries')
            ->assertSee('1 article')
            ->assertDontSee('The next entry is being studied.')
            ->assertSee('>Blog<', false);
        $journalContent = $journalResponse->getContent();
        $this->assertLessThan(strpos($journalContent, 'Why risk belongs before entries'), strpos($journalContent, 'The latest notes.'));
        $this->get(route('journal.category', $category))->assertOk()->assertSee('Why risk belongs before entries')->assertSee('data-journal-view="grid"', false)->assertSee('data-journal-view="list"', false);
        $this->get(route('journal.tag', $tag))->assertOk()->assertSee('Why risk belongs before entries');
        $this->get(route('journal.author', $member))->assertOk()->assertSee('Why risk belongs before entries');
        $this->get(route('journal.show', $article))->assertOk()
            ->assertSee('Related social content')
            ->assertSee('instagram.com/p/example')
            ->assertSee('Risk Management')
            ->assertDontSee('class="public-hero-breadcrumbs"', false)
            ->assertSee('In this note')
            ->assertSee('id="build-the-plan"', false)
            ->assertSee('id="start-with-risk"', false)
            ->assertSee('application/ld+json')
            ->assertDontSee('#InternalTag');
        $this->get(route('feed'))->assertOk()->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')->assertSee('Why risk belongs before entries');
    }

    public function test_blog_listing_filters_sort_and_view_state_use_published_records(): void
    {
        $member = TeamMember::where('slug', 'lenkai-mollel')->firstOrFail();
        $category = ArticleCategory::where('is_active', true)->firstOrFail();
        $tag = Tag::create(['name' => 'Risk Process', 'slug' => 'risk-process-listing']);

        $oldArticle = Article::create([
            'article_category_id' => $category->id,
            'team_member_id' => $member->id,
            'title' => 'Alpha risk note',
            'slug' => 'alpha-risk-note',
            'excerpt' => 'An older risk process note.',
            'content' => '<p>Risk process and planning for a disciplined trading workflow.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);
        $newArticle = Article::create([
            'article_category_id' => $category->id,
            'team_member_id' => $member->id,
            'title' => 'Beta risk note',
            'slug' => 'beta-risk-note',
            'excerpt' => 'A newer risk process note.',
            'content' => '<p>Risk process and execution for a disciplined trading workflow.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $oldArticle->tags()->attach($tag);
        $newArticle->tags()->attach($tag);
        Article::create([
            'article_category_id' => $category->id,
            'team_member_id' => $member->id,
            'title' => 'Draft risk note',
            'slug' => 'draft-risk-note',
            'content' => '<p>This draft must never appear on the public listing.</p>',
            'status' => 'draft',
        ]);

        $response = $this->get(route('blog', [
            'search' => 'risk',
            'category' => $category->slug,
            'tag' => $tag->slug,
            'sort' => 'oldest',
            'view' => 'list',
        ]));

        $response->assertOk()
            ->assertSee('Alpha risk note')
            ->assertSee('Beta risk note')
            ->assertDontSee('Draft risk note')
            ->assertSee('name="sort"', false)
            ->assertSee('aria-label="List view"', false)
            ->assertSee('noindex,nofollow', false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Beta risk note'), strpos($content, 'Alpha risk note'));

        for ($index = 1; $index <= 11; $index++) {
            $article = Article::create([
                'article_category_id' => $category->id,
                'team_member_id' => $member->id,
                'title' => 'Additional risk note '.$index,
                'slug' => 'additional-risk-note-'.$index,
                'excerpt' => 'A risk process note for pagination.',
                'content' => '<p>Risk process and planning for a disciplined trading workflow.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(10 + $index),
            ]);
            $article->tags()->attach($tag);
        }

        $this->get(route('blog', ['search' => 'risk', 'tag' => $tag->slug]))
            ->assertOk()
            ->assertDontSee('Draft risk note')
            ->assertSee('Blog pagination', false)
            ->assertSee('page=2', false);

        $this->get(route('blog', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee('noindex,nofollow', false);
        $this->get(route('blog.tag', $tag))->assertOk()->assertSee('Alpha risk note');
    }

    public function test_video_posts_use_a_listing_fallback_without_loading_a_player(): void
    {
        Storage::fake('public');
        $member = TeamMember::where('slug', 'lenkai-mollel')->firstOrFail();
        Storage::disk('public')->put('media/journal-video.mp4', 'video-bytes');
        Media::create(['disk' => 'public', 'path' => 'media/journal-video.mp4', 'filename' => 'journal-video.mp4', 'mime_type' => 'video/mp4', 'size' => 11]);
        $article = Article::create([
            'team_member_id' => $member->id,
            'title' => 'A featured video note',
            'slug' => 'a-featured-video-note',
            'excerpt' => 'A media-backed journal update.',
            'content' => '<p>Video content.</p>',
            'featured_image' => 'media/journal-video.mp4',
            'status' => 'published',
            'published_at' => now(),
            'is_featured' => true,
            'feature_priority' => 100,
        ]);

        $response = $this->get(route('journal'));
        $response->assertOk()
            ->assertDontSee('<video', false)
            ->assertSee('Video')
            ->assertDontSee(asset('storage/media/journal-video.mp4'), false);
        $this->assertSame(3, substr_count($response->getContent(), route('blog.show', $article)));
    }

    public function test_authenticated_reactions_and_comments_are_moderated_and_safe(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'avatar_path' => 'customer-avatars/comment-author.png']);
        $article = Article::create(['title' => 'A considered note', 'slug' => 'a-considered-note', 'content' => '<p>Read carefully.</p>', 'status' => 'published', 'published_at' => now()->subMinute()]);

        $this->actingAs($user)->post(route('journal.reaction', $article), ['type' => 'like'])->assertRedirect();
        $this->assertDatabaseHas('article_reactions', ['article_id' => $article->id, 'user_id' => $user->id, 'type' => 'like']);
        $this->actingAs($user)->post(route('journal.reaction', $article), ['type' => 'like'])->assertRedirect();
        $this->assertDatabaseMissing('article_reactions', ['article_id' => $article->id, 'user_id' => $user->id, 'type' => 'like']);

        $this->actingAs($user)->post(route('journal.comments.store', $article), ['body' => '<script>alert(1)</script> A useful thought'])->assertRedirect();
        $comment = Comment::firstOrFail();
        $this->assertSame('pending', $comment->status);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'body' => '<script>alert(1)</script> A useful thought']);
        $commentPage = $this->actingAs($user)->get(route('journal.show', $article));
        $commentPage->assertSee('A useful thought')->assertSee('Awaiting moderation');
        $this->assertLessThan(strpos($commentPage->getContent(), 'comment-form-card'), strpos($commentPage->getContent(), 'A useful thought'));
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), ['status' => 'approved'])->assertRedirect();
        $this->get(route('journal.show', $article))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; A useful thought', false)->assertSee('src="/storage/customer-avatars/comment-author.png"', false)->assertDontSee('<script>alert(1)</script>', false);

        $otherUser = User::factory()->create(['is_admin' => false]);
        $this->actingAs($otherUser)->post(route('journal.comments.store', $article), ['parent_id' => $comment->id, 'body' => 'A reply from another reader'])->assertRedirect();
        $reply = Comment::where('body', 'A reply from another reader')->firstOrFail();
        $this->assertSame($comment->id, $reply->parent_id);

        $this->actingAs($admin)->patch(route('admin.comments.update', $reply), ['status' => 'approved'])->assertRedirect();
        $thirdUser = User::factory()->create(['is_admin' => false]);
        $this->actingAs($thirdUser)->post(route('journal.comments.store', $article), ['parent_id' => $reply->id, 'body' => 'A reply to the reply'])->assertRedirect();
        $nestedReply = Comment::where('body', 'A reply to the reply')->firstOrFail();
        $this->assertSame($reply->id, $nestedReply->parent_id);

        $this->actingAs($otherUser)->get(route('journal.show', $article))
            ->assertSee('A reply from another reader')
            ->assertSee('Reply');
    }

    public function test_authenticated_comment_can_toggle_and_switch_thumb_reactions_and_admin_sees_counts(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['is_admin' => false]);
        $article = Article::create(['title' => 'A note with reactions', 'slug' => 'a-note-with-reactions', 'content' => '<p>Read and respond.</p>', 'status' => 'published', 'published_at' => now()->subMinute()]);
        $comment = Comment::create(['article_id' => $article->id, 'user_id' => $otherUser->id, 'body' => 'A useful reader perspective.', 'status' => 'approved', 'approved_at' => now()]);

        $this->actingAs($user)->postJson(route('comments.reaction', $comment), ['type' => 'up'])
            ->assertOk()->assertJsonPath('active', true)->assertJsonPath('user_type', 'up')->assertJsonPath('up_count', 1)->assertJsonPath('down_count', 0);
        $this->assertDatabaseHas('comment_reactions', ['comment_id' => $comment->id, 'user_id' => $user->id, 'type' => 'up']);

        $this->actingAs($user)->postJson(route('comments.reaction', $comment), ['type' => 'down'])
            ->assertOk()->assertJsonPath('active', true)->assertJsonPath('user_type', 'down')->assertJsonPath('up_count', 0)->assertJsonPath('down_count', 1);
        $this->assertDatabaseHas('comment_reactions', ['comment_id' => $comment->id, 'user_id' => $user->id, 'type' => 'down']);
        $this->assertDatabaseMissing('comment_reactions', ['comment_id' => $comment->id, 'user_id' => $user->id, 'type' => 'up']);

        $this->actingAs($user)->postJson(route('comments.reaction', $comment), ['type' => 'down'])
            ->assertOk()->assertJsonPath('active', false)->assertJsonPath('user_type', null)->assertJsonPath('down_count', 0);

        $this->actingAs($otherUser)->post(route('comments.reaction', $comment), ['type' => 'up'])->assertRedirect();
        $page = $this->actingAs($user)->get(route('journal.show', $article));
        $page->assertSee('comment-reaction-button')->assertSee('Thumbs up this comment')->assertSee('data-comment-reaction-count="up"', false);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.comments'))->assertOk()->assertSee('Reactions')->assertSee('↑ 1', false);
        $this->actingAs($admin)->get(route('admin.comments.show', $comment))->assertOk()->assertSee('↑ 1', false);
    }

    public function test_admin_managed_open_graph_media_reaches_detail_metadata(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        Storage::disk('public')->put('media/course-social.jpg', 'image-bytes');
        $media = Media::create(['disk' => 'public', 'path' => 'media/course-social.jpg', 'filename' => 'course-social.jpg', 'mime_type' => 'image/jpeg', 'size' => 11, 'alt_text' => 'Course social image']);
        $course = Course::firstOrFail();

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'title' => $course->title, 'slug' => $course->slug, 'status' => $course->status, 'short_description' => $course->short_description,
            'full_description' => $course->full_description, 'og_image' => $media->path, 'canonical_url' => 'https://example.com/managed-course', 'robots' => 'noindex,nofollow', 'og_title' => 'Managed course social title', 'og_description' => 'Managed course social description.', 'hero_overlay' => 'medium', 'hero_alignment' => 'left',
        ])->assertRedirect();

        $this->get(route('courses.show', $course))->assertOk()->assertSee(asset('storage/'.$media->path))->assertSee('https://example.com/managed-course')->assertSee('noindex,nofollow')->assertSee('Managed course social title');
    }

    public function test_administrator_management_tracks_access_and_protects_final_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'active']);

        $this->actingAs($admin)->get(route('admin.administrators'))->assertOk()->assertSee($admin->email);
        $this->actingAs($admin)->put(route('admin.administrators.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'status' => 'suspended',
        ])->assertSessionHasErrors('status');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status' => 'active']);

        $this->actingAs($admin)->post(route('admin.administrators.store'), [
            'name' => 'Desk Operator', 'email' => 'operator@example.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'status' => 'active',
        ])->assertRedirect();
        $operator = User::where('email', 'operator@example.com')->firstOrFail();
        $this->assertTrue($operator->is_admin);

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.login.store'), ['email' => $operator->email, 'password' => 'password123'])->assertRedirect(route('admin.dashboard'));
        $this->assertNotNull($operator->fresh()->last_login_at);
    }

    public function test_suspended_administrator_cannot_sign_in(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'status' => 'suspended']);

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_review_customers_and_update_account_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['name' => 'Student Customer', 'email' => 'student@example.com', 'is_admin' => false, 'status' => 'active']);

        $this->actingAs($admin)->get(route('admin.customers'))->assertOk()->assertSee('Student Customer')->assertSee('student@example.com');
        $this->actingAs($admin)->get(route('admin.search', ['q' => 'student@example.com']))->assertOk()->assertSee('Customer')->assertSee(route('admin.customers.show', $customer));
        $this->actingAs($admin)->get(route('admin.customers.show', $customer))->assertOk()->assertSee('Account status')->assertSee('Account details')->assertSee('Downloads')->assertSee('Student Customer');
        $this->actingAs($admin)->patch(route('admin.customers.status', $customer), ['status' => 'suspended'])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status' => 'suspended']);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'customer.status_updated', 'auditable_id' => $customer->id]);
        $customer->forceFill(['password' => 'password', 'email_verified_at' => now()])->save();
        Auth::logout();
        $this->post(route('login.store'), ['email' => $customer->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_create_update_and_delete_customer_accounts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.customers.create'))->assertOk()->assertSee('Add a customer');
        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'name' => 'Managed Customer',
            'email' => 'managed@example.com',
            'phone' => '+255 700 000 000',
            'country' => 'tz',
            'password' => 'customer-password',
            'password_confirmation' => 'customer-password',
            'status' => 'active',
        ])->assertRedirect();

        $customer = User::where('email', 'managed@example.com')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.customers.edit', $customer))->assertOk()->assertSee('Edit customer account');
        $this->actingAs($admin)->put(route('admin.customers.update', $customer), [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '',
            'country' => 'TZ',
            'password' => '',
            'password_confirmation' => '',
            'status' => 'suspended',
        ])->assertRedirect(route('admin.customers.show', $customer));

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'Updated Customer', 'email' => 'updated@example.com', 'status' => 'suspended', 'country' => 'TZ']);
        $this->actingAs($admin)->delete(route('admin.customers.destroy', $customer))->assertRedirect(route('admin.customers'));
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
    }

    public function test_admin_learning_topic_hero_and_seo_propagate_to_public_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        Storage::disk('public')->put('media/topic-hero.jpg', 'image-bytes');
        $media = Media::create(['disk' => 'public', 'path' => 'media/topic-hero.jpg', 'filename' => 'topic-hero.jpg', 'mime_type' => 'image/jpeg', 'size' => 11, 'alt_text' => 'Topic hero']);
        $topic = LearningTopic::where('slug', 'forex-core-basics')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.topics.update', $topic), [
            'title' => $topic->title, 'slug' => $topic->slug, 'short_description' => $topic->short_description,
            'full_description' => $topic->full_description, 'hero_eyebrow' => 'A managed learning path', 'hero_title' => 'Study the market deliberately',
            'hero_summary' => 'A hero summary controlled by the CMS.', 'hero_image' => $media->path, 'hero_overlay' => 'strong', 'hero_alignment' => 'center',
            'seo_title' => 'Managed learning SEO title', 'seo_description' => 'Managed learning SEO description.', 'og_image' => $media->path, 'hero_focal_point' => 'left bottom',
            'skill_level' => $topic->skill_level, 'study_time' => $topic->study_time, 'sort_order' => $topic->sort_order, 'is_published' => 1,
        ])->assertRedirect();

        $this->get(route('learn.show', $topic))->assertOk()->assertSee('Study the market deliberately')->assertSee('Managed learning SEO title')->assertSee(asset('storage/'.$media->path))->assertSee('--hero-image-position: left bottom');
    }

    public function test_published_page_slug_changes_create_safe_redirects(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::create(['key' => 'redirect-test', 'slug' => 'old-name', 'name' => 'Redirect test', 'page_type' => 'standard', 'status' => 'published', 'is_visible' => true, 'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'hero_title' => 'Old page']);
        $this->get('/pages/old-name')->assertSuccessful()->assertSee('Old page');
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => 'new-name', 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'hero_title' => 'New page',
        ])->assertRedirect();
        $this->assertDatabaseHas('redirects', ['source_path' => '/pages/old-name', 'destination_path' => '/pages/new-name']);
        $this->get('/pages/old-name')->assertRedirect('/pages/new-name');
        $this->get('/pages/new-name')->assertSuccessful()->assertSee('New page');
    }

    public function test_draft_articles_are_not_public(): void
    {
        $category = ArticleCategory::first();
        $article = Article::create(['article_category_id' => $category->id, 'title' => 'Private draft', 'slug' => 'private-draft', 'content' => 'Not ready', 'status' => 'draft']);
        $this->get('/journal/private-draft')->assertNotFound();
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'draft']);
    }

    public function test_sitemap_contains_public_content(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('/financial-academy/courses/forex-foundations')->assertSee('/financial-academy/tools/trading-journal-sheet')->assertSee('/student-of-money')->assertSee('/about/team/lenkai-mollel')->assertSee('/about/team/david-lyengi');
    }

    public function test_team_members_are_cms_managed_and_inactive_profiles_stay_private(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = TeamMember::where('slug', 'lenkai-mollel')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.team'))->assertOk()->assertSee('Lenkai Mollel');
        $this->actingAs($admin)->put(route('admin.team.update', $member), [
            'name' => $member->name, 'slug' => $member->slug, 'short_intro' => 'A managed team introduction.',
            'portrait' => $member->portrait, 'sort_order' => 1, 'is_active' => 1, 'is_featured' => 1,
            'linkedin_url' => 'https://www.linkedin.com/in/lenkai-mollel',
        ])->assertRedirect();

        $this->get(route('team.show', $member->slug))->assertOk()->assertSee('A managed team introduction.')->assertSee('linkedin.com/in/lenkai-mollel')->assertSee('application/ld+json');
        $member->update(['is_active' => false]);
        $this->get(route('team.show', $member->slug))->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/about/team/'.$member->slug);
    }

    public function test_contact_submission_is_saved(): void
    {
        $this->post('/contact', ['name' => 'A Student', 'email' => 'student@example.com', 'category' => 'general', 'message' => 'I would like to learn more.', 'consent' => '1'])->assertRedirect('/contact');
        $this->assertDatabaseHas('contact_messages', ['email' => 'student@example.com', 'category' => 'general']);
    }

    public function test_admin_dashboard_requires_an_admin_user(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $admin = User::factory()->create(['is_admin' => true]);
        AdminAuditLog::create(['user_id' => $admin->id, 'action' => 'course.updated', 'summary' => 'A real dashboard event']);
        $this->actingAs($admin)->get('/admin')->assertSuccessful()->assertSee('Operational overview')->assertSee('Needs attention')->assertSee('A real dashboard event');
        $this->actingAs($admin)->get(route('admin.search', ['q' => 'Forex']))->assertSuccessful()->assertSee('Search the desk')->assertSee('Forex');
    }

    public function test_admin_can_manage_course_modules_product_features_and_article_tags(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = Course::firstOrFail();
        $product = Product::firstOrFail();
        $category = ArticleCategory::firstOrFail();

        $this->actingAs($admin)->get(route('admin.courses.edit', $course))->assertSuccessful()->assertSee('Modules and lessons');
        $this->actingAs($admin)->get(route('admin.products.edit', $product))->assertSuccessful()->assertSee('Features and versions');
        $this->actingAs($admin)->get(route('admin.articles.create'))->assertSuccessful()->assertSee('Category');
        $this->actingAs($admin)->get(route('admin.topics.create'))->assertSuccessful()->assertSee('New learning topic');
        $this->actingAs($admin)->post(route('admin.topics.store'), ['title' => 'A new learning path', 'short_description' => 'A clear path for students.', 'full_description' => '<p>Safe topic content.</p><script>alert(1)</script>', 'learning_outcomes' => "Write rules\nReview decisions", 'course_ids' => [Course::firstOrFail()->id], 'product_ids' => [Product::firstOrFail()->id], 'sort_order' => 10])->assertRedirect();
        $this->assertDatabaseHas('learning_topics', ['slug' => 'a-new-learning-path', 'status' => 'draft']);

        $this->actingAs($admin)->post(route('admin.courses.modules.store', $course), ['title' => 'A careful first module', 'description' => 'Module description', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_modules', ['course_id' => $course->id, 'title' => 'A careful first module']);
        $module = $course->modules()->firstOrFail();
        $this->actingAs($admin)->post(route('admin.modules.lessons.store', $module), ['title' => 'Lesson one', 'content' => 'Lesson content', 'sort_order' => 1, 'is_published' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_lessons', ['course_module_id' => $module->id, 'slug' => 'lesson-one']);
        $lesson = $module->lessons()->firstOrFail();
        $this->actingAs($admin)->put(route('admin.modules.update', $module), ['title' => 'A renamed first module', 'description' => 'Updated module description', 'sort_order' => 2])->assertRedirect();
        $this->actingAs($admin)->put(route('admin.lessons.update', $lesson), ['title' => 'A revised lesson', 'content' => '<p>Safe <strong>lesson</strong>.</p><script>alert(1)</script>', 'sort_order' => 2, 'is_published' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_modules', ['id' => $module->id, 'title' => 'A renamed first module', 'sort_order' => 2]);
        $this->assertDatabaseHas('course_lessons', ['id' => $lesson->id, 'title' => 'A revised lesson', 'content' => '<p>Safe <strong>lesson</strong>.</p>']);
        $this->actingAs($admin)->delete(route('admin.lessons.destroy', $lesson))->assertRedirect();
        $this->assertSoftDeleted('course_lessons', ['id' => $lesson->id]);
        $this->actingAs($admin)->post(route('admin.lessons.restore', $lesson->id))->assertRedirect();
        $this->assertDatabaseHas('course_lessons', ['id' => $lesson->id, 'deleted_at' => null]);
        $this->actingAs($admin)->delete(route('admin.modules.destroy', $module))->assertRedirect();
        $this->assertSoftDeleted('course_modules', ['id' => $module->id]);
        $this->actingAs($admin)->post(route('admin.modules.restore', $module->id))->assertRedirect();
        $this->assertDatabaseHas('course_modules', ['id' => $module->id, 'deleted_at' => null]);

        $this->actingAs($admin)->post(route('admin.products.features.store', $product), ['title' => 'Clear risk inputs', 'description' => 'A focused feature', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('product_features', ['product_id' => $product->id, 'title' => 'Clear risk inputs']);
        Storage::fake('public');
        $this->actingAs($admin)->post(route('admin.products.images.store', $product), ['file' => UploadedFile::fake()->create('screen.png', 10, 'image/png'), 'alt_text' => 'Risk planner screenshot'])->assertRedirect();
        $this->assertDatabaseHas('product_images', ['product_id' => $product->id, 'alt_text' => 'Risk planner screenshot']);
        $this->actingAs($admin)->post(route('admin.courses.faqs.store', $course), ['question' => 'Who is this for?', 'answer' => 'Careful students.', 'sort_order' => 1])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.products.faqs.store', $product), ['question' => 'When is it ready?', 'answer' => 'When the process is ready.', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_faqs', ['course_id' => $course->id, 'question' => 'Who is this for?']);
        $this->assertDatabaseHas('faqs', ['question' => 'When is it ready?']);

        $this->actingAs($admin)->post(route('admin.articles.store'), ['article_category_id' => $category->id, 'title' => 'A real field note', 'content' => 'A careful note.', 'status' => 'draft', 'tags' => 'risk, process'])->assertRedirect();
        $this->assertDatabaseHas('tags', ['slug' => 'risk']);
        $this->assertDatabaseHas('tags', ['slug' => 'process']);

        $this->actingAs($admin)->get(route('admin.categories'))->assertSuccessful()->assertSee('Article categories');
        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Portfolio Notes'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.tags.store'), ['name' => 'Discipline'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.social-links.store'), ['label' => 'Instagram', 'url' => 'https://instagram.com/example', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('social_links', ['label' => 'Instagram']);

        $this->actingAs($admin)->delete(route('admin.courses.destroy', $course))->assertRedirect();
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $this->actingAs($admin)->post(route('admin.courses.restore', $course->id))->assertRedirect();
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'deleted_at' => null]);
    }

    public function test_admin_boundary_and_signed_previews_are_enforced(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $course = Course::firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);
        $previewUrl = URL::temporarySignedRoute('admin.preview', now()->addMinutes(5), ['type' => 'course', 'id' => $course->id]);
        Auth::logout();
        $this->get($previewUrl)->assertRedirect('/admin/login');
        $this->actingAs($admin)->get($previewUrl)->assertOk()->assertSee('Private draft preview')->assertSee($course->title);
        $this->actingAs($admin)->get(route('admin.preview', ['type' => 'course', 'id' => $course->id]))->assertForbidden();
    }

    public function test_media_settings_messages_and_slug_rules_work(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        $this->actingAs($admin)->post(route('admin.media.store'), ['file' => UploadedFile::fake()->create('editorial.jpg', 120, 'image/jpeg'), 'alt_text' => 'Editorial desk', 'title' => 'Editorial desk'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.media.store'), ['file' => UploadedFile::fake()->create('unsafe.svg', 10, 'image/svg+xml'), 'alt_text' => 'Unsafe vector'])->assertSessionHasErrors('file');
        $this->assertDatabaseHas('media', ['filename' => 'editorial.jpg', 'alt_text' => 'Editorial desk']);
        $this->actingAs($admin)->get(route('admin.media.search', ['q' => 'editorial']))->assertOk()->assertJsonPath('0.label', 'editorial.jpg');
        $this->actingAs($admin)->get(route('admin.media', ['q' => 'editorial', 'type' => 'image']))->assertOk()->assertSee('Editorial desk');

        $this->actingAs($admin)->put(route('admin.settings.update'), ['brand_name' => 'Mwanafunzi Test', 'contact_email' => 'desk@example.com', 'contact_phone' => '+255700000000', 'contact_whatsapp' => '+255711111111', 'contact_location' => 'Dar es Salaam', 'logo' => '', 'dark_logo' => 'https://cdn.example.test/mwanafunzi-dark.svg', 'light_logo' => 'https://cdn.example.test/mwanafunzi-light.svg', 'icon_logo' => '', 'favicon' => '', 'apple_touch_icon' => '', 'default_social_image' => '', 'footer_copy' => 'Study. Test. Review.', 'footer_copyright' => 'Copyright managed from settings', 'footer_bottom_statement' => 'PROCESS OVER HYPE.', 'default_seo_title' => 'Test title', 'default_seo_description' => 'Test description', 'risk_disclaimer' => 'Test disclaimer.'])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('desk@example.com')->assertSee('WhatsApp +255711111111')->assertSee('Study. Test. Review.')->assertSee('Copyright managed from settings')->assertSee('PROCESS OVER HYPE.')->assertSee('https://cdn.example.test/mwanafunzi-light.svg')->assertSee('https://cdn.example.test/mwanafunzi-dark.svg');

        $this->post('/contact', ['name' => 'Operations', 'email' => 'ops@example.com', 'category' => 'support', 'message' => 'Please help with the platform.', 'consent' => '1'])->assertRedirect();
        $message = ContactMessage::where('email', 'ops@example.com')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.messages.update', $message), ['status' => 'resolved'])->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'resolved']);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'message.status_updated', 'auditable_id' => $message->id]);
        $this->actingAs($admin)->get(route('admin.messages', ['q' => 'ops@example.com', 'status' => 'resolved']))->assertOk()->assertSee('ops@example.com');
        $this->actingAs($admin)->get(route('admin.messages.show', $message))->assertOk()->assertSee($message->message)->assertSee('Contact details');
        $this->assertNotNull($message->fresh()->read_at);

        $this->actingAs($admin)->post(route('admin.courses.store'), ['title' => 'Duplicate course', 'slug' => 'forex-foundations', 'status' => 'draft', 'short_description' => 'Should fail'])->assertSessionHasErrors('slug');
        $this->assertDatabaseMissing('courses', ['title' => 'Duplicate course']);
        $this->assertDatabaseHas('site_settings', ['key' => 'contact_email', 'value' => 'desk@example.com']);
    }

    public function test_new_post_media_field_can_upload_and_preview_images_or_videos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');

        $this->actingAs($admin)
            ->get(route('admin.articles.create'))
            ->assertOk()
            ->assertSee('Featured image or video')
            ->assertSee('data-media-upload-input', false)
            ->assertSee('data-media-upload-progress', false);

        $imageResponse = $this->actingAs($admin)->postJson(route('admin.media.inline.store'), [
            'file' => UploadedFile::fake()->image('inline-editorial.jpg', 640, 360),
        ])->assertCreated();
        $imagePath = $imageResponse->json('path');
        $this->assertDatabaseHas('media', ['path' => $imagePath, 'mime_type' => 'image/jpeg', 'uploaded_by' => $admin->id]);
        Storage::disk('public')->assertExists($imagePath);

        $videoResponse = $this->actingAs($admin)->postJson(route('admin.media.inline.store'), [
            'file' => UploadedFile::fake()->create('inline-editorial.mp4', 10, 'video/mp4'),
        ])->assertCreated();
        $videoPath = $videoResponse->json('path');
        $this->assertDatabaseHas('media', ['path' => $videoPath, 'mime_type' => 'video/mp4', 'uploaded_by' => $admin->id]);
        Storage::disk('public')->assertExists($videoPath);
    }

    public function test_media_file_deletion_is_blocked_while_referenced_but_record_only_removal_is_available(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = TeamMember::where('slug', 'lenkai-mollel')->firstOrFail();
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/team-portrait.jpg',
            'filename' => 'team-portrait.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'alt_text' => 'Team portrait',
        ]);
        $member->update(['portrait' => $media->path]);

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media), ['delete_files' => '1'])
            ->assertRedirect()
            ->assertSessionHasErrors('media');
        $this->assertDatabaseHas('media', ['id' => $media->id]);

        Storage::fake('public');
        Storage::disk('public')->put($media->path, 'referenced asset');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media), ['delete_files' => '0'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Media record removed; stored files were kept.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
        $this->assertDatabaseHas('team_members', ['id' => $member->id, 'portrait' => $media->path]);
    }

    public function test_admin_can_delete_media_record_and_all_stored_contents_when_unreferenced(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/original.jpg',
            'filename' => 'original.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'alt_text' => 'Original asset',
            'variants' => [['path' => 'media/variants/original-480.webp']],
        ]);
        Storage::disk('public')->put($media->path, 'original');
        Storage::disk('public')->put('media/variants/original-480.webp', 'variant');

        $this->actingAs($admin)
            ->get(route('admin.media'))
            ->assertOk()
            ->assertSee('Remove record only')
            ->assertSee('Delete files too');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media), ['delete_files' => '1'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Media and its stored files were deleted.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing('media/original.jpg');
        Storage::disk('public')->assertMissing('media/variants/original-480.webp');
    }

    public function test_admin_can_change_media_status_and_nonpublished_media_is_hidden_publicly(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/status-check.jpg',
            'filename' => 'status-check.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'alt_text' => 'Status check asset',
        ]);
        Storage::disk('public')->put($media->path, 'asset');

        $this->actingAs($admin)
            ->put(route('admin.media.update', $media), [
                'status' => 'unpublished',
                'alt_text' => 'Status check asset',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('media', ['id' => $media->id, 'status' => 'unpublished']);
        $this->actingAs($admin)->get(route('admin.media', ['status' => 'unpublished']))->assertOk()->assertSee('status-check.jpg');
        $this->assertNull(\App\Support\PublicHero::candidate($media->path));

        $this->actingAs($admin)
            ->put(route('admin.media.update', $media), [
                'status' => 'archived',
                'alt_text' => 'Status check asset',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('media', ['id' => $media->id, 'status' => 'archived']);
    }

    public function test_module_enquiries_are_saved_with_business_unit(): void
    {
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();

        $this->post('/contact', [
            'name' => 'Studio Client',
            'email' => 'studio@example.com',
            'business_unit_id' => $studio->id,
            'category' => 'partnership',
            'message' => 'I would like to discuss a studio project.',
            'consent' => '1',
        ])->assertRedirect('/contact');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'studio@example.com',
            'business_unit_id' => $studio->id,
        ]);
    }

    public function test_admin_can_manage_business_module_metadata_without_changing_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $studio = BusinessUnit::where('slug', 'studio')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.business-units'))->assertOk()->assertSee('Business modules')->assertSee('Creative Studio')->assertSee('Edit Financial Academy page');
        $this->actingAs($admin)->get(route('admin.pages'))->assertOk()->assertSee('Financial Academy');
        $this->actingAs($admin)->put(route('admin.business-units.update', $studio), [
            'name' => 'Mwanafunzi Studio',
            'tagline' => 'Stories with a point of view.',
            'description' => 'Updated studio description from the operating desk.',
            'accent_color' => '#aa5533',
            'sort_order' => 4,
            'is_active' => 1,
            'slug' => 'changed-by-client',
            'route_name' => 'admin.dashboard',
        ])->assertRedirect();

        $this->assertDatabaseHas('business_units', [
            'id' => $studio->id,
            'slug' => 'studio',
            'route_name' => 'studio',
            'name' => 'Mwanafunzi Studio',
            'accent_color' => '#aa5533',
        ]);
        $this->get('/contact')->assertOk()->assertSee('Mwanafunzi Studio');
    }

    public function test_student_and_admin_portals_render_the_dashboard_shells(): void
    {
        $student = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($student)->get(route('account.dashboard'))->assertOk()->assertSee('Student portal')->assertSee('Getting started')->assertSee('Explore learning')->assertSee('Explore digital tools')->assertSee('Explore courses');
        foreach ([route('account.orders'), route('account.downloads'), route('account.courses'), route('account.profile'), route('account.security')] as $accountRoute) {
            $this->get($accountRoute)->assertOk()->assertSee('Student portal');
        }

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Admin desk')->assertSee('Recent activity')->assertSee('Quick actions');
    }

    public function test_admin_shell_exposes_consistent_feedback_and_action_regions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->withSession(['success' => 'Course saved.'])
            ->actingAs($admin)
            ->get(route('admin.courses'))
            ->assertOk()
            ->assertSee('admin-feedback-stack')
            ->assertSee('Course saved.')
            ->assertSee('admin-page-header-actions')
            ->assertSee('data-admin-fullscreen')
            ->assertSee('data-admin-notifications-toggle')
            ->assertSee('data-admin-search-input')
            ->assertSee(route('admin.profile'))
            ->assertSee('Courses');
    }

    public function test_admin_pages_use_shared_dashboard_form_and_control_primitives(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('admin-stat-card')
            ->assertSee('admin-page-header-actions')
            ->assertSee('Operational overview')
            ->assertSee('Needs attention')
            ->assertSee('Recent activity')
            ->assertSee('Recent enquiries')
            ->assertSee('Platform overview')
            ->assertSee('New page')
            ->assertSee('Upload media');
        $this->actingAs($admin)->get(route('admin.commerce.dashboard'))
            ->assertOk()
            ->assertSee('admin-stat-card')
            ->assertSee('Confirmed revenue');
        $this->actingAs($admin)->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('admin-form-section')
            ->assertSee('Design tokens');
        $this->actingAs($admin)->get(route('admin.business-units'))
            ->assertOk()
            ->assertSee('admin-toggle')
            ->assertSee('Visible and accepting enquiries');
    }

    public function test_admin_search_notifications_and_profile_features_use_real_data(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'name' => 'Desk Administrator']);

        $this->actingAs($admin)->get(route('admin.search', ['q' => 'Forex']))
            ->assertOk()->assertSee('Search the desk')->assertSee('Forex');
        $this->actingAs($admin)->get(route('admin.search.suggestions', ['q' => 'Forex']))
            ->assertOk()->assertJsonStructure(['total', 'groups']);

        $this->post('/contact', ['name' => 'Notification Student', 'email' => 'notification@example.com', 'category' => 'general', 'message' => 'A real notification enquiry.', 'consent' => '1'])->assertRedirect();
        $message = ContactMessage::where('email', 'notification@example.com')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.notifications', ['filter' => 'unread']))
            ->assertOk()->assertSee('Notification Student')->assertSee('Unread');
        $this->actingAs($admin)->post(route('admin.notifications.read', $message))
            ->assertRedirect(route('admin.messages.show', $message));
        $this->assertNotNull($message->fresh()->read_at);

        Storage::fake('public');
        $this->actingAs($admin)
            ->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Personal information')
            ->assertSee('Password and security')
            ->assertSee('Upload cropped photo')
            ->assertSee('data-avatar-crop-modal')
            ->assertSee('data-avatar-crop-canvas');
        $this->actingAs($admin)->put(route('admin.profile.update'), ['name' => 'Desk Lead', 'phone' => '+255700000000', 'job_title' => 'Platform lead', 'department' => 'Operations', 'bio' => 'A careful administrator.'])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => 'Desk Lead', 'job_title' => 'Platform lead']);
        $this->actingAs($admin)->post(route('admin.profile.avatar.update'), ['avatar' => UploadedFile::fake()->image('avatar.png')])->assertRedirect();
        $this->assertNotNull($admin->fresh()->avatar_path);
        $admin->update(['avatar_path' => 'admin-avatars/desk-lead.png']);
        $this->actingAs($admin)->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('src="/storage/admin-avatars/desk-lead.png"', false)
            ->assertSee('admin-profile-avatar', false);
        $this->actingAs($admin)->delete(route('admin.profile.avatar.remove'))->assertRedirect();
        $this->assertNull($admin->fresh()->avatar_path);
        $this->actingAs($admin)->put(route('admin.profile.password'), ['current_password' => 'password', 'password' => 'new-password-123', 'password_confirmation' => 'new-password-123'])->assertRedirect();
    }

    public function test_all_static_admin_get_routes_render_for_an_authorised_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with((string) $route->getName(), 'admin.')
                && in_array('GET', $route->methods(), true)
                && ! str_contains($route->uri(), '{'))
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        foreach ($routes as $routeName) {
            $response = $this->actingAs($admin)->get(route($routeName));
            $this->assertLessThan(500, $response->getStatusCode(), $routeName.' returned a server error.');
            $this->assertNotSame(404, $response->getStatusCode(), $routeName.' returned not found.');
        }
    }

    public function test_pages_navigation_and_design_tokens_propagate_to_public_frontend(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::where('key', 'about')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.pages'))->assertOk()->assertSee('Pages');
        $this->actingAs($admin)->get(route('admin.policies'))->assertOk()->assertSee('Policies')->assertSee('Privacy Policy');
        $this->actingAs($admin)->get(route('admin.pages.edit', $page))->assertOk()->assertSee('Hero image');
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_eyebrow' => 'Updated from the CMS', 'hero_title' => 'A page managed from the desk', 'hero_summary' => 'CMS summary', 'hero_overlay' => 'medium', 'hero_alignment' => 'left',
            'seo_title' => 'CMS about title', 'seo_description' => 'CMS about description',
        ])->assertRedirect();
        $philosophy = $page->sections()->where('key', 'philosophy')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'sections' => [$philosophy->id => [
                'heading' => 'A different kind of education, managed from the CMS.', 'body' => 'Updated page content from the structured editor.', 'sort_order' => 10, 'is_enabled' => 1,
            ]],
        ])->assertRedirect();
        $this->get('/about')->assertOk()->assertSee('A page managed from the desk')->assertSee('A different kind of education, managed from the CMS.')->assertSee('CMS about title');

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_primary_label' => 'Read our approach', 'hero_primary_url' => '#approach', 'hero_secondary_label' => 'Contact the desk', 'hero_secondary_url' => '/contact',
            'hero_overlay' => 'medium', 'hero_alignment' => 'left',
        ])->assertRedirect();
        $this->get('/about')->assertOk()->assertSee('Read our approach')->assertSee('Contact the desk');

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_primary_label' => 'Unsafe', 'hero_primary_url' => 'javascript:alert(1)', 'hero_overlay' => 'medium', 'hero_alignment' => 'left',
        ])->assertSessionHasErrors('hero_primary_url');

        $terms = Page::where('key', 'terms')->firstOrFail();
        $acceptance = $terms->sections()->where('key', 'acceptance')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $terms), [
            'name' => $terms->name, 'key' => $terms->key, 'slug' => $terms->slug, 'page_type' => $terms->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'sections' => [$acceptance->id => [
                'heading' => 'Acceptance of the updated terms', 'body' => 'This policy section is now controlled by the structured CMS editor.', 'sort_order' => 10, 'is_enabled' => 1,
            ]],
        ])->assertRedirect();
        $this->get('/terms')->assertOk()->assertSee('Acceptance of the updated terms')->assertSee('This policy section is now controlled by the structured CMS editor.');

        $this->actingAs($admin)->get(route('admin.faqs'))->assertOk()->assertSee('Frequently asked questions');
        $this->actingAs($admin)->post(route('admin.faqs.store'), [
            'target_type' => 'page', 'target_id' => $terms->id, 'question' => 'Where can I ask about these terms?', 'answer' => 'Contact the desk for clarification.', 'sort_order' => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('faqs', ['faqable_type' => Page::class, 'faqable_id' => $terms->id, 'question' => 'Where can I ask about these terms?']);
        $this->get('/terms')->assertOk()->assertSee('Where can I ask about these terms?')->assertSee('Contact the desk for clarification.');

        $home = Page::where('key', 'home')->firstOrFail();
        $learning = $home->sections()->where('key', 'learning')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $home), [
            'name' => $home->name, 'key' => $home->key, 'slug' => $home->slug, 'page_type' => $home->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_title' => 'Decisions become', 'hero_highlight' => 'a practice.', 'hero_summary' => 'A CMS-managed homepage hero.',
            'hero_primary_label' => 'Open the curriculum', 'hero_primary_url' => '#courses', 'hero_secondary_label' => 'Read the journal', 'hero_secondary_url' => '#journal',
            'hero_note' => 'Study • Plan • Review', 'hero_aside' => "For careful students.\nNot predictions.", 'hero_aside_index' => '02 / 04',
            'hero_overlay' => 'strong', 'hero_alignment' => 'left', 'sections' => [$learning->id => [
                'heading' => $learning->heading, 'body' => $learning->body, 'sort_order' => 20,
            ]],
        ])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Decisions become')->assertSee('a practice.')->assertSee('Open the curriculum')->assertSee('Study • Plan • Review')->assertDontSee($learning->heading);

        $framework = $home->sections()->where('key', 'framework')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $home), [
            'name' => $home->name, 'key' => $home->key, 'slug' => $home->slug, 'page_type' => $home->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'strong', 'hero_alignment' => 'left', 'sections' => [$framework->id => [
                'heading' => $framework->heading, 'body' => $framework->body, 'sort_order' => 30, 'is_enabled' => 1,
                'steps' => "Study | Read the market with patience.",
            ]],
        ])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Study');

        $philosophy = $home->sections()->where('key', 'philosophy')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $home), [
            'name' => $home->name, 'key' => $home->key, 'slug' => $home->slug, 'page_type' => $home->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'strong', 'hero_alignment' => 'left', 'sections' => [$philosophy->id => [
                'eyebrow' => 'A CMS-managed philosophy', 'heading' => $philosophy->heading, 'body' => $philosophy->body, 'sort_order' => 10, 'is_enabled' => 1,
                'cards' => 'A new pillar | Managed without Blade. |',
            ]],
        ])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('A new pillar')->assertSee('Managed without Blade.')->assertDontSee($philosophy->heading);

        $this->actingAs($admin)->put(route('admin.pages.update', $home), [
            'name' => $home->name, 'key' => $home->key, 'slug' => $home->slug, 'page_type' => $home->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'strong', 'hero_alignment' => 'left', 'sections' => [$framework->id => [
                'sort_order' => 100, 'is_enabled' => 1,
            ]],
        ])->assertRedirect();
        $homepage = $this->get('/')->assertOk()->getContent();
        $this->assertLessThan(strpos($homepage, 'The Student of Money Framework.'), strpos($homepage, 'Make the process visible.'));

        $navigation = NavigationItem::where('location', 'header')->where('label', 'About')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.navigation'))->assertOk()->assertSee('Navigation');
        $this->actingAs($admin)->put(route('admin.navigation.update', $navigation), [
            'location' => 'header', 'menu_group' => 'primary', 'label' => 'Our story', 'route_name' => 'about', 'url' => '', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => $navigation->sort_order, 'is_visible' => 1,
        ])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Our story');

        // Saving an existing navigation item without changing visibility must keep it public.
        $this->actingAs($admin)->put(route('admin.navigation.update', $navigation), [
            'location' => 'header', 'menu_group' => 'primary', 'label' => 'Our story updated', 'route_name' => 'about', 'url' => '', 'target' => '_self', 'cta_style' => 'link', 'sort_order' => $navigation->sort_order,
        ])->assertRedirect();
        $this->assertDatabaseHas('navigation_items', ['id' => $navigation->id, 'label' => 'Our story updated', 'is_visible' => 1]);
        $this->get('/')->assertOk()->assertSee('Our story updated');

        $settings = ['brand_name' => 'Mwanafunzi Investor', 'contact_email' => 'desk@example.com', 'default_seo_title' => 'CMS title', 'default_seo_description' => 'CMS description', 'risk_disclaimer' => 'CMS disclaimer.', 'design_copper' => '#aa5533', 'design_container_max_width' => 1200];
        $this->actingAs($admin)->put(route('admin.settings.update'), $settings)->assertRedirect();
        $this->assertDatabaseHas('site_settings', ['key' => 'design_copper', 'value' => '#aa5533']);
        $this->get('/')->assertOk()->assertSee('--copper:#aa5533');

        $this->actingAs($admin)->post(route('admin.social-links.store'), ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/mwanafunzi', 'sort_order' => 1, 'is_visible' => 1])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('LinkedIn');
        $this->actingAs($admin)->get(route('admin.audit'))->assertOk()->assertSee('Updated site settings');
    }

    public function test_media_metadata_and_page_sections_are_editable_from_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        $media = Media::create(['disk' => 'public', 'path' => 'media/test.jpg', 'filename' => 'test.jpg', 'mime_type' => 'image/jpeg', 'size' => 100, 'alt_text' => 'Old alt', 'title' => 'Old title']);

        $this->actingAs($admin)->get(route('admin.media', ['view' => 'list']))->assertOk()->assertSee('test.jpg');
        $this->actingAs($admin)->put(route('admin.media.update', $media), ['title' => 'Updated title', 'alt_text' => 'Updated alt text', 'caption' => 'Updated caption', 'file' => UploadedFile::fake()->create('replacement.png', 100, 'image/png')])->assertRedirect();
        $this->assertDatabaseHas('media', ['id' => $media->id, 'path' => 'media/test.jpg', 'filename' => 'replacement.png', 'title' => 'Updated title', 'alt_text' => 'Updated alt text', 'caption' => 'Updated caption']);

        $page = Page::where('key', 'about')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'new_section_key' => 'editorial-note', 'new_section_type' => 'rich_text',
            'new_section_heading' => 'A new controlled section', 'new_section_body' => '<p>This section was added through the admin interface.</p><script>alert(1)</script>', 'new_section_sort_order' => 30, 'new_section_enabled' => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('page_sections', ['page_id' => $page->id, 'key' => 'editorial-note', 'body' => '<p>This section was added through the admin interface.</p>']);
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'draft', 'is_visible' => 0,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left',
        ])->assertRedirect();
        $this->get('/about')->assertNotFound();
    }
}
