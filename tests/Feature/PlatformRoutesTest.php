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
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PlatformRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_public_platform_routes_render(): void
    {
        foreach (['/', '/development', '/studio', '/learn', '/courses', '/tools', '/journal', '/about', '/contact', '/student-of-money', '/risk-disclosure', '/privacy-policy', '/terms', '/refund-policy', '/disclaimer', '/sitemap.xml'] as $route) {
            $this->get($route)->assertSuccessful();
        }
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
    }

    public function test_published_and_coming_soon_content_resolves_by_slug(): void
    {
        $this->get('/courses/forex-foundations')->assertSuccessful()->assertSee('Forex Foundations')->assertSee('application/ld+json')->assertSee('canonical');
        $this->get('/tools/trading-journal-sheet')->assertSuccessful()->assertSee('Trading Journal Sheet')->assertSee('application/ld+json');
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
        $this->get('/sitemap.xml')->assertOk()->assertSee('/courses/forex-foundations')->assertSee('/tools/trading-journal-sheet')->assertSee('/student-of-money');
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
        $this->actingAs($admin)->get('/admin')->assertSuccessful()->assertSee('Content management');
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
        $this->actingAs($admin)->post(route('admin.topics.store'), ['title' => 'A new learning path', 'short_description' => 'A clear path for students.', 'full_description' => '<p>Safe topic content.</p><script>alert(1)</script>', 'sort_order' => 10])->assertRedirect();
        $this->assertDatabaseHas('learning_topics', ['slug' => 'a-new-learning-path', 'status' => 'draft']);

        $this->actingAs($admin)->post(route('admin.courses.modules.store', $course), ['title' => 'A careful first module', 'description' => 'Module description', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_modules', ['course_id' => $course->id, 'title' => 'A careful first module']);
        $module = $course->modules()->firstOrFail();
        $this->actingAs($admin)->post(route('admin.modules.lessons.store', $module), ['title' => 'Lesson one', 'content' => 'Lesson content', 'sort_order' => 1, 'is_published' => 1])->assertRedirect();
        $this->assertDatabaseHas('course_lessons', ['course_module_id' => $module->id, 'slug' => 'lesson-one']);

        $this->actingAs($admin)->post(route('admin.products.features.store', $product), ['title' => 'Clear risk inputs', 'description' => 'A focused feature', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('product_features', ['product_id' => $product->id, 'title' => 'Clear risk inputs']);
        Storage::fake('public');
        $this->actingAs($admin)->post(route('admin.products.images.store', $product), ['file' => UploadedFile::fake()->create('screen.svg', 10, 'image/svg+xml'), 'alt_text' => 'Risk planner screenshot'])->assertRedirect();
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
        $this->assertDatabaseHas('media', ['filename' => 'editorial.jpg', 'alt_text' => 'Editorial desk']);
        $this->actingAs($admin)->get(route('admin.media', ['q' => 'editorial', 'type' => 'image']))->assertOk()->assertSee('Editorial desk');

        $this->actingAs($admin)->put(route('admin.settings.update'), ['brand_name' => 'Mwanafunzi Test', 'contact_email' => 'desk@example.com', 'contact_phone' => '+255700000000', 'contact_whatsapp' => '+255711111111', 'contact_location' => 'Dar es Salaam', 'logo' => '', 'dark_logo' => '', 'light_logo' => '', 'icon_logo' => '', 'favicon' => '', 'apple_touch_icon' => '', 'default_social_image' => '', 'footer_copy' => 'Study. Test. Review.', 'footer_copyright' => 'Copyright managed from settings', 'footer_bottom_statement' => 'PROCESS OVER HYPE.', 'default_seo_title' => 'Test title', 'default_seo_description' => 'Test description', 'risk_disclaimer' => 'Test disclaimer.'])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('desk@example.com')->assertSee('WhatsApp +255711111111')->assertSee('Study. Test. Review.')->assertSee('Copyright managed from settings')->assertSee('PROCESS OVER HYPE.');

        $this->post('/contact', ['name' => 'Operations', 'email' => 'ops@example.com', 'category' => 'support', 'message' => 'Please help with the platform.', 'consent' => '1'])->assertRedirect();
        $message = ContactMessage::where('email', 'ops@example.com')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.messages.update', $message), ['status' => 'resolved'])->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'resolved']);

        $this->actingAs($admin)->post(route('admin.courses.store'), ['title' => 'Duplicate course', 'slug' => 'forex-foundations', 'status' => 'draft', 'short_description' => 'Should fail'])->assertSessionHasErrors('slug');
        $this->assertDatabaseMissing('courses', ['title' => 'Duplicate course']);
        $this->assertDatabaseHas('site_settings', ['key' => 'contact_email', 'value' => 'desk@example.com']);
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

    public function test_student_and_admin_portals_render_the_dashboard_shells(): void
    {
        $student = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($student)->get(route('account.dashboard'))->assertOk()->assertSee('Student portal')->assertSee('Course access');

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Admin desk')->assertSee('Content management');
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

        $terms = Page::where('key', 'terms')->firstOrFail();
        $acceptance = $terms->sections()->where('key', 'acceptance')->firstOrFail();
        $this->actingAs($admin)->put(route('admin.pages.update', $terms), [
            'name' => $terms->name, 'key' => $terms->key, 'slug' => $terms->slug, 'page_type' => $terms->page_type, 'status' => 'published', 'is_visible' => 1,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left', 'sections' => [$acceptance->id => [
                'heading' => 'Acceptance of the updated terms', 'body' => 'This policy section is now controlled by the structured CMS editor.', 'sort_order' => 10, 'is_enabled' => 1,
            ]],
        ])->assertRedirect();
        $this->get('/terms')->assertOk()->assertSee('Acceptance of the updated terms')->assertSee('This policy section is now controlled by the structured CMS editor.');

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
        $this->get('/')->assertOk()->assertSee('A new pillar')->assertSee('Managed without Blade.');

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
            'new_section_heading' => 'A new controlled section', 'new_section_body' => 'This section was added through the admin interface.', 'new_section_sort_order' => 30, 'new_section_enabled' => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('page_sections', ['page_id' => $page->id, 'key' => 'editorial-note']);
        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => $page->name, 'key' => $page->key, 'slug' => $page->slug, 'page_type' => $page->page_type, 'status' => 'draft', 'is_visible' => 0,
            'hero_overlay' => 'medium', 'hero_alignment' => 'left',
        ])->assertRedirect();
        $this->get('/about')->assertNotFound();
    }
}
