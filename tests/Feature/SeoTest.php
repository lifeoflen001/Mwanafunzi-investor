<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Course;
use App\Models\Page;
use App\Models\User;
use App\Support\HeroFocalPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_homepage_has_one_metadata_set_and_website_schema(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<meta name="description"'));
        $this->assertSame(1, substr_count($html, '<link rel="canonical"'));
        $this->assertStringContainsString('"@type":"WebSite"', $html);
        $this->assertStringContainsString('og:site_name', $html);
    }

    public function test_sitemap_contains_lastmod_and_excludes_unpublished_articles(): void
    {
        $draft = Article::create(['title' => 'Private draft for sitemap', 'slug' => 'private-draft-for-sitemap', 'content' => '<p>Draft content.</p>', 'content_type' => 'article', 'status' => 'draft', 'published_at' => null]);
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $this->assertStringContainsString('<lastmod>', $response->getContent());
        $this->assertStringNotContainsString($draft->slug, $response->getContent());
        $this->assertStringNotContainsString('/feed', $response->getContent());
    }

    public function test_homepage_structured_data_is_valid_json(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

        $this->assertNotEmpty($matches[1]);
        foreach ($matches[1] as $document) {
            $decoded = json_decode(html_entity_decode($document), true);
            $this->assertIsArray($decoded);
            $this->assertArrayHasKey('@type', $decoded);
        }
    }

    public function test_public_html_site_map_is_crawlable(): void
    {
        $this->get('/site-map')->assertOk()->assertSee('Find your way around.')->assertSee(route('blog'))->assertSee(route('legal', 'privacy-policy'));
    }

    public function test_seo_dashboard_and_sitemap_cover_the_same_public_urls(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.seo'))->assertOk();

        $dashboard = collect($this->app->make(\App\Http\Controllers\AdminSeoController::class)->index()->getData()['pages'])
            ->pluck('url')->map(fn (string $url) => rtrim($url, '/'))->sort()->values();
        preg_match_all('/<loc>(.*?)<\\/loc>/', $this->get('/sitemap.xml')->getContent(), $matches);
        $sitemap = collect($matches[1])->map(fn (string $url) => rtrim($url, '/'))->sort()->values();

        $this->assertSame($sitemap->all(), $dashboard->all());
        $builtInPageUrls = Page::whereIn('key', ['privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer', 'cookie-policy'])
            ->pluck('id')->map(fn ($id) => rtrim(route('pages.show', ['slug' => $id]), '/'));
        foreach ($builtInPageUrls as $url) {
            $this->assertFalse($dashboard->contains($url));
        }
        $this->assertTrue($sitemap->contains(fn (string $url) => str_ends_with($url, '/financial-academy')));
        $this->assertTrue($sitemap->contains(fn (string $url) => str_ends_with($url, '/site-map')));
        $this->assertFalse($sitemap->contains(fn (string $url) => str_ends_with($url, '/studio')));
    }

    public function test_admin_seo_health_view_is_protected_and_renders(): void
    {
        $this->get(route('admin.seo'))->assertRedirect(route('admin.login'));

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.seo'))->assertOk()->assertSee('SEO management')->assertSee('Sitemap and robots');
    }

    public function test_course_seo_metadata_persists_through_admin_editor(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = Course::create(['title' => 'SEO test course', 'slug' => 'seo-test-course', 'status' => 'draft', 'short_description' => 'A course used to verify SEO persistence.', 'hero_focal_point' => HeroFocalPoint::DEFAULT]);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'title' => $course->title,
            'slug' => $course->slug,
            'hero_focal_point' => HeroFocalPoint::DEFAULT,
            'status' => 'draft',
            'short_description' => $course->short_description,
            'primary_keyword' => 'forex trading for beginners Tanzania',
            'secondary_keywords' => 'risk management course, kozi ya forex',
            'seo_title' => 'Forex Trading for Beginners — Mwanafunzi Investor',
            'seo_description' => 'A factual introduction to deliberate forex trading study and risk management.',
            'canonical_url' => 'https://mwanafunziinvestor.com/financial-academy/courses/seo-test-course',
            'robots' => 'index,follow',
            'og_title' => 'Forex Trading for Beginners',
            'og_description' => 'Study market structure and risk before taking the next step.',
        ])->assertRedirect(route('admin.courses'));

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'primary_keyword' => 'forex trading for beginners Tanzania', 'secondary_keywords' => 'risk management course, kozi ya forex']);
    }
}
