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
        foreach (['/', '/learn', '/courses', '/tools', '/journal', '/about', '/contact', '/student-of-money', '/risk-disclosure', '/privacy-policy', '/terms', '/refund-policy', '/disclaimer', '/sitemap.xml'] as $route) {
            $this->get($route)->assertSuccessful();
        }
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

        $this->actingAs($admin)->put(route('admin.settings.update'), ['brand_name' => 'Mwanafunzi Test', 'contact_email' => 'desk@example.com', 'contact_phone' => '+255700000000', 'contact_whatsapp' => '+255700000000', 'contact_location' => 'Dar es Salaam', 'logo' => '', 'dark_logo' => '', 'favicon' => '', 'default_social_image' => '', 'footer_copy' => 'Study. Test. Review.', 'default_seo_title' => 'Test title', 'default_seo_description' => 'Test description', 'risk_disclaimer' => 'Test disclaimer.'])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('desk@example.com')->assertSee('Study. Test. Review.');

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
}
