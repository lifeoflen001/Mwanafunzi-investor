<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\StudioInquiry;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\DevelopmentDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DevelopmentDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_development_demo_seeder_is_idempotent_and_marks_fixtures(): void
    {
        $this->seed(DevelopmentDemoSeeder::class);
        $counts = $this->demoCounts();

        $this->seed(DevelopmentDemoSeeder::class);

        $this->assertSame($counts, $this->demoCounts());
        $this->assertSame(1, User::where('email', 'demo.client@mwanafunziinvestor.test')->count());
        $this->assertSame(1, Order::where('order_number', 'DEMO-ORDER-0001')->count());
        $this->assertSame(1, Page::where('key', 'demo-audit-page')->where('status', 'draft')->count());
        $this->assertSame(1, StudioInquiry::where('reference', 'DEMO-STUDIO-0001')->count());
        $this->assertTrue(Testimonial::where('client_name', '[DEMO] Example Photography')->where('is_published', false)->exists());
        $this->assertTrue(ContactMessage::where('email', 'demo.enquirer@example.test')->exists());
        $this->assertTrue(NewsletterSubscriber::where('email', 'demo.subscriber@example.test')->exists());
        $this->assertTrue(Article::where('slug', 'demo-article-for-qa')->where('status', 'draft')->exists());
        $this->assertTrue(Course::where('slug', 'demo-course-for-qa')->where('status', 'draft')->exists());
        $this->assertTrue(Product::where('slug', 'demo-product-for-qa')->where('availability', 'draft')->exists());
        $this->assertTrue(Project::where('slug', 'demo-studio-project')->where('status', 'draft')->exists());
        $this->assertSame(2, Service::where('slug', 'like', 'demo-%')->count());
    }

    public function test_development_demo_seeder_refuses_production_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('limited to local, testing, and staging');

        app(DevelopmentDemoSeeder::class)->run();
    }

    private function demoCounts(): array
    {
        return [
            'users' => User::where('email', 'like', 'demo.%')->count(),
            'services' => Service::where('slug', 'like', 'demo-%')->count(),
            'projects' => Project::where('slug', 'demo-studio-project')->count(),
            'testimonials' => Testimonial::where('client_name', 'like', '[DEMO]%')->count(),
            'courses' => Course::where('slug', 'demo-course-for-qa')->count(),
            'articles' => Article::where('slug', 'demo-article-for-qa')->count(),
            'products' => Product::where('slug', 'demo-product-for-qa')->count(),
            'orders' => Order::where('order_number', 'DEMO-ORDER-0001')->count(),
            'messages' => ContactMessage::where('email', 'demo.enquirer@example.test')->count(),
            'subscribers' => NewsletterSubscriber::where('email', 'demo.subscriber@example.test')->count(),
            'inquiries' => StudioInquiry::where('reference', 'DEMO-STUDIO-0001')->count(),
            'pages' => Page::where('key', 'demo-audit-page')->count(),
        ];
    }
}
