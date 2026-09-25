<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Product;
use App\Models\Page;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class PublicSiteController extends Controller
{
    public function module(string $slug)
    {
        $module = BusinessUnit::query()->active()->where('slug', $slug)->firstOrFail();
        $page = $this->cmsPage($slug);

        if ($module->route_name === 'home') {
            return to_route('home');
        }

        if ($module->slug === 'development') {
            return view('public.modules.development', compact('module', 'page'));
        }

        if ($module->slug === 'studio') {
            return view('public.modules.studio', compact('module', 'page'));
        }

        return view('public.modules.show', compact('module', 'page'));
    }

    public function home()
    {
        $homePage = $this->cmsPage('home');
        return view('welcome', [
            'homePage' => $homePage,
            'learningTopics' => LearningTopic::query()->where('is_published', true)->orderBy('sort_order')->get(),
            'courses' => Course::published()->orderBy('sort_order')->get(),
            'products' => Product::available()->orderBy('sort_order')->get(),
            'featuredArticle' => Article::published()->with('category')->where('is_featured', true)->latest('published_at')->first(),
        ]);
    }

    public function learn()
    {
        $topics = LearningTopic::with('courses')->where('is_published', true)->orderBy('sort_order')->get();
        return view('public.learn', ['topics' => $topics, 'page' => $this->cmsPage('learn')]);
    }

    public function topic(LearningTopic $topic)
    {
        abort_unless($topic->is_published && $topic->status === 'published', 404);
        $topic->load(['courses' => fn ($query) => $query->published()->orderBy('sort_order'), 'articles' => fn ($query) => $query->published()->latest('published_at')->limit(3), 'products' => fn ($query) => $query->available()->orderBy('sort_order')->limit(3), 'faqs']);
        return view('public.learn-show', compact('topic'));
    }

    public function courses()
    {
        $courses = Course::published()->withCount('modules')->orderBy('sort_order')->paginate(12);
        return view('public.courses.index', ['courses' => $courses, 'page' => $this->cmsPage('courses')]);
    }

    public function course(Course $course)
    {
        abort_unless(in_array($course->status, ['published', 'open', 'coming_soon'], true), 404);
        $course->load(['modules.lessons', 'faqs']);
        return view('public.courses.show', compact('course'));
    }

    public function preview(string $type, int $id)
    {
        if ($type === 'course') {
            $course = Course::withTrashed()->with(['modules.lessons', 'faqs'])->findOrFail($id);
            return view('public.courses.show', compact('course'))->with('preview', true);
        }
        if ($type === 'product') {
            $product = Product::withTrashed()->with(['features', 'images', 'versions', 'faqs'])->findOrFail($id);
            return view('public.tools.show', ['product' => $product, 'relatedProducts' => collect(), 'preview' => true]);
        }
        if ($type === 'article') {
            $article = Article::withTrashed()->with(['category', 'tags'])->findOrFail($id);
            return view('public.journal.show', ['article' => $article, 'relatedArticles' => collect(), 'preview' => true]);
        }
        if ($type === 'topic') {
            $topic = LearningTopic::withTrashed()->findOrFail($id);
            return view('public.learn-preview', compact('topic'))->with('preview', true);
        }
        if ($type === 'page') {
            $page = Page::withTrashed()->with(['sections', 'faqs'])->findOrFail($id);
            return view('public.pages.cms', compact('page'))->with('preview', true);
        }
        abort(404);
    }

    public function pageBySlug(string $slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (! $page) {
            $redirect = Redirect::where('source_path', '/pages/'.$slug)->where('is_enabled', true)->first();
            if ($redirect) return redirect()->to($redirect->destination_path, $redirect->status_code);
            abort(404);
        }
        abort_unless($page->status === 'published' && $page->is_visible && (! $page->published_at || $page->published_at->isPast()), 404);
        $page->load(['sections', 'faqs']);
        return view('public.pages.cms', compact('page'));
    }

    public function tools()
    {
        $products = Product::available()->with('features')->orderBy('sort_order')->paginate(12);
        return view('public.tools.index', ['products' => $products, 'page' => $this->cmsPage('tools')]);
    }

    public function tool(Product $product)
    {
        abort_unless(in_array($product->availability, ['available', 'coming_soon', 'waitlist'], true), 404);
        $product->load(['features', 'images', 'versions', 'faqs']);
        $relatedProducts = Product::available()->whereKeyNot($product->id)->orderBy('sort_order')->limit(3)->get();
        return view('public.tools.show', compact('product', 'relatedProducts'));
    }

    public function journal(Request $request)
    {
        $categories = ArticleCategory::orderBy('name')->get();
        $articles = Article::published()->with('category');
        if ($request->filled('category')) {
            $articles->whereHas('category', fn ($query) => $query->where('slug', $request->string('category')));
        }
        if ($request->filled('q')) {
            $search = $request->string('q');
            $articles->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%"));
        }
        $articles = $articles->latest('published_at')->paginate(9)->withQueryString();
        return view('public.journal.index', ['articles' => $articles, 'categories' => $categories, 'page' => $this->cmsPage('journal')]);
    }

    public function article(Article $article)
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $article->load(['category', 'tags']);
        $relatedArticles = Article::published()->whereKeyNot($article->id)->where('article_category_id', $article->article_category_id)->latest('published_at')->limit(3)->get();
        return view('public.journal.show', compact('article', 'relatedArticles'));
    }

    public function contact()
    {
        $businessUnits = BusinessUnit::query()->active()->orderBy('sort_order')->get();
        $selectedBusinessUnit = $businessUnits->firstWhere('slug', request('module'));

        return view('public.contact', ['businessUnits' => $businessUnits, 'selectedBusinessUnit' => $selectedBusinessUnit, 'page' => $this->cmsPage('contact')]);
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'business_unit_id' => ['nullable', 'integer', Rule::exists('business_units', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'category' => ['required', 'in:general,course,product,partnership,support,other'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['prohibited'],
        ]);
        ContactMessage::create([...$validated, 'consented_at' => now()]);
        return to_route('contact')->with('success', 'Thank you. Your message has been received by the Mwanafunzi desk.');
    }

    public function page(string $page)
    {
        abort_unless(in_array($page, ['about', 'student-of-money', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer'], true), 404);
        $cmsPage = $this->cmsPage($page);
        return view("public.pages.{$page}", ['page' => $cmsPage]);
    }

    public function sitemap()
    {
        $urls = collect([
            route('home'), route('learn'), route('courses'), route('tools'), route('journal'), route('about'), route('contact'), route('student-of-money'),
            route('legal', 'privacy-policy'), route('legal', 'terms'), route('legal', 'risk-disclosure'), route('legal', 'refund-policy'), route('legal', 'disclaimer'),
        ]);
        $urls = $urls->merge(BusinessUnit::active()->whereNotNull('route_name')->where('route_name', '!=', 'home')->get()->map(fn ($businessUnit) => route($businessUnit->route_name)));
        $urls = $urls->merge(LearningTopic::where('is_published', true)->where('status', 'published')->get()->map(fn ($topic) => route('learn.show', $topic)));
        $urls = $urls->merge(Course::published()->get()->map(fn ($course) => route('courses.show', $course)));
        $urls = $urls->merge(Product::available()->get()->map(fn ($product) => route('tools.show', $product)));
        $urls = $urls->merge(Article::published()->get()->map(fn ($article) => route('journal.show', $article)));
        $urls = $urls->merge(Page::published()->whereNotNull('slug')->whereNotIn('key', ['learn', 'courses', 'tools', 'journal', 'about', 'contact', 'student-of-money', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer'])->get()->map(fn ($page) => route('pages.show', $page)));
        $xml = view('seo.sitemap', ['urls' => $urls])->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }

    private function cmsPage(string $key): ?Page
    {
        $page = Page::where('key', $key)->first();
        if (! $page) return null;
        abort_unless($page->status === 'published' && $page->is_visible && (! $page->published_at || $page->published_at->isPast()), 404);
        return $page->load(['sections', 'faqs']);
    }
}
