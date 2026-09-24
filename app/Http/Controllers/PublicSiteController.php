<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PublicSiteController extends Controller
{
    public function home()
    {
        return view('welcome', [
            'learningTopics' => LearningTopic::query()->where('is_published', true)->orderBy('sort_order')->get(),
            'courses' => Course::published()->orderBy('sort_order')->get(),
            'products' => Product::available()->orderBy('sort_order')->get(),
            'featuredArticle' => Article::published()->with('category')->where('is_featured', true)->latest('published_at')->first(),
        ]);
    }

    public function learn()
    {
        $topics = LearningTopic::with('courses')->where('is_published', true)->orderBy('sort_order')->get();
        return view('public.learn', compact('topics'));
    }

    public function courses()
    {
        $courses = Course::published()->withCount('modules')->orderBy('sort_order')->paginate(12);
        return view('public.courses.index', compact('courses'));
    }

    public function course(Course $course)
    {
        abort_unless(in_array($course->status, ['published', 'coming_soon'], true), 404);
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
        abort(404);
    }

    public function tools()
    {
        $products = Product::available()->with('features')->orderBy('sort_order')->paginate(12);
        return view('public.tools.index', compact('products'));
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
        return view('public.journal.index', compact('articles', 'categories'));
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
        return view('public.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
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
        return view("public.pages.{$page}");
    }

    public function sitemap()
    {
        $urls = collect([
            route('home'), route('learn'), route('courses'), route('tools'), route('journal'), route('about'), route('contact'), route('student-of-money'),
            route('legal', 'privacy-policy'), route('legal', 'terms'), route('legal', 'risk-disclosure'), route('legal', 'refund-policy'), route('legal', 'disclaimer'),
        ]);
        $urls = $urls->merge(Course::published()->get()->map(fn ($course) => route('courses.show', $course)));
        $urls = $urls->merge(Product::available()->get()->map(fn ($product) => route('tools.show', $product)));
        $urls = $urls->merge(Article::published()->get()->map(fn ($article) => route('journal.show', $article)));
        $xml = view('seo.sitemap', ['urls' => $urls])->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }
}
