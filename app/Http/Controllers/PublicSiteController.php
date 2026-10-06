<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\BusinessUnit;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Product;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use App\Support\PublicHero;

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
        $featuredProjects = Project::published()->where('is_featured', true)->with(['services', 'gallery'])->orderBy('display_order')->limit(4)->get();
        $featuredServices = Service::active()->where('is_featured', true)->orderBy('sort_order')->limit(4)->get();
        PublicHero::preloadMedia($featuredProjects->pluck('featured_image')->merge($featuredServices->pluck('featured_image')));

        return view('welcome', [
            'homePage' => $homePage,
            'products' => Product::available()->orderBy('sort_order')->limit(3)->get(),
            'featuredArticle' => Article::featured()->with('category')->first(),
            'featuredServices' => $featuredServices,
            'featuredProjects' => $featuredProjects,
            'featuredTestimonials' => Testimonial::published()->where('is_featured', true)->with('project')->orderBy('sort_order')->limit(3)->get(),
        ]);
    }

    public function services()
    {
        $services = Service::active()->with('projects')->orderBy('sort_order')->orderBy('title')->get();
        $featuredProjects = Project::published()->where('is_featured', true)->with('services')->orderBy('display_order')->limit(3)->get();
        PublicHero::preloadMedia($services->pluck('featured_image')->merge($featuredProjects->pluck('featured_image')));

        return view('public.services.index', [
            'services' => $services,
            'featuredProjects' => $featuredProjects,
            'businessUnits' => BusinessUnit::active()->orderBy('sort_order')->get(),
            'serviceRoutes' => ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')],
            'page' => $this->cmsPage('services'),
        ]);
    }

    public function service(Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load(['projects' => fn ($query) => $query->published()->with('services')->orderBy('display_order')]);
        PublicHero::preloadMedia([$service->featured_image, ...$service->projects->pluck('featured_image')->all()]);
        return view('public.services.show', ['service' => $service, 'relatedServices' => Service::active()->whereKeyNot($service->id)->orderBy('sort_order')->limit(3)->get()]);
    }

    public function projects()
    {
        $projects = Project::published()->with('services')->orderBy('display_order')->orderByDesc('project_date')->paginate(12);
        PublicHero::preloadMedia($projects->getCollection()->pluck('featured_image'));
        return view('public.projects.index', ['projects' => $projects, 'page' => $this->cmsPage('projects')]);
    }

    public function project(Project $project)
    {
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);
        $project->load(['services', 'gallery', 'testimonials' => fn ($query) => $query->published()->orderBy('sort_order')]);
        $relatedProjects = Project::published()->whereKeyNot($project->id)->with('services')->orderBy('display_order')->limit(3)->get();
        PublicHero::preloadMedia([$project->featured_image, ...$relatedProjects->pluck('featured_image')->all()]);
        return view('public.projects.show', ['project' => $project, 'relatedProjects' => $relatedProjects]);
    }

    public function academy()
    {
        $page = $this->cmsPage('learn');
        $homePage = $this->cmsPage('home');

        return view('public.landing.forex-academy', [
            'page' => $page,
            'homePage' => $homePage,
            'topics' => LearningTopic::query()->where('is_published', true)->orderBy('sort_order')->get(),
            'courses' => Course::published()->orderBy('sort_order')->get(),
            'products' => Product::available()->orderBy('sort_order')->get(),
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
            $article = Article::withTrashed()->with(['category', 'tags', 'socialLinks', 'authorMember'])->withCount(['reactions as likes_count', 'approvedComments as comments_count'])->findOrFail($id);
            return view('public.journal.show', ['article' => $article, 'relatedArticles' => collect(), 'comments' => collect(), 'userHasLiked' => false, 'preview' => true]);
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
        return $this->editorialIndex($request);
    }

    public function journalCategory(Request $request, ArticleCategory $category)
    {
        return $this->editorialIndex($request, $category);
    }

    public function journalTag(Request $request, Tag $tag)
    {
        return $this->editorialIndex($request, null, $tag);
    }

    public function journalAuthor(Request $request, TeamMember $member)
    {
        abort_unless($member->is_active, 404);
        return $this->editorialIndex($request, null, null, $member);
    }

    public function article(Article $article)
    {
        abort_unless(in_array($article->status, ['published', 'scheduled'], true) && $article->published_at?->isPast(), 404);
        $article->load(['category', 'tags', 'socialLinks', 'authorMember']);
        $article->loadCount(['reactions as likes_count', 'approvedComments as comments_count']);
        $comments = $article->comments()->approved()->whereNull('parent_id')->with(['user', 'replies' => fn ($query) => $query->approved()->with('user')->latest()])->latest()->get();
        $relatedArticles = Article::published()->with(['category', 'authorMember'])->whereKeyNot($article->id)
            ->when($article->article_category_id, fn ($query) => $query->where('article_category_id', $article->article_category_id))
            ->latest('published_at')->limit(3)->get();
        return view('public.journal.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'comments' => $comments,
            'userHasLiked' => auth()->check() && $article->reactions()->where('user_id', auth()->id())->where('type', 'like')->exists(),
        ]);
    }

    public function feed()
    {
        $articles = Article::published()->with(['category', 'authorMember'])->latest('published_at')->limit(30)->get();
        return response()->view('public.feed', compact('articles'))->header('Content-Type', 'application/rss+xml; charset=UTF-8');
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
        $data = ['page' => $cmsPage];
        if ($page === 'about') $data['teamMembers'] = $this->teamMembers();
        return view("public.pages.{$page}", $data);
    }

    public function teamMember(string $member)
    {
        $member = TeamMember::active()->where('slug', $member)->firstOrFail();

        return view('public.team.show', compact('member'));
    }

    public function sitemap()
    {
        $urls = collect([
            route('home'), route('forex-academy'), route('digital-systems'), route('creative-studio'), route('services'), route('projects'), route('learn'), route('courses'), route('tools'), route('journal'), route('feed'), route('about'), route('contact'), route('student-of-money'),
            route('legal', 'privacy-policy'), route('legal', 'terms'), route('legal', 'risk-disclosure'), route('legal', 'refund-policy'), route('legal', 'disclaimer'),
        ]);
        $urls = $urls->merge(TeamMember::active()->get()->map(fn ($member) => route('team.show', $member->slug)));
        $urls = $urls->merge(BusinessUnit::active()->whereNotNull('route_name')->where('route_name', '!=', 'home')->get()->map(fn ($businessUnit) => route($businessUnit->route_name)));
        $urls = $urls->merge(LearningTopic::where('is_published', true)->where('status', 'published')->get()->map(fn ($topic) => route('learn.show', $topic)));
        $urls = $urls->merge(Course::published()->get()->map(fn ($course) => route('courses.show', $course)));
        $urls = $urls->merge(Product::available()->get()->map(fn ($product) => route('tools.show', $product)));
        $urls = $urls->merge(Article::published()->get()->map(fn ($article) => route('journal.show', $article)));
        $urls = $urls->merge(ArticleCategory::whereHas('articles', fn ($query) => $query->published())->get()->map(fn ($category) => route('journal.category', $category)));
        $urls = $urls->merge(Tag::whereHas('articles', fn ($query) => $query->published())->get()->map(fn ($tag) => route('journal.tag', $tag)));
        $urls = $urls->merge(TeamMember::active()->whereHas('articles', fn ($query) => $query->published())->get()->map(fn ($member) => route('journal.author', $member)));
        $urls = $urls->merge(Service::active()->get()->map(fn ($service) => route('services.show', $service)));
        $urls = $urls->merge(Project::published()->get()->map(fn ($project) => route('projects.show', $project)));
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

    private function teamMembers()
    {
        return TeamMember::active()->orderBy('sort_order')->orderBy('name')->get();
    }

    private function editorialIndex(Request $request, ?ArticleCategory $category = null, ?Tag $tag = null, ?TeamMember $author = null)
    {
        $search = trim((string) ($request->input('search') ?: $request->input('q')));
        $categories = ArticleCategory::withCount(['articles as published_articles_count' => fn ($query) => $query->published()])->orderBy('name')->get();
        $articlesQuery = Article::published()->with(['category', 'authorMember'])
            ->withCount(['reactions as likes_count', 'approvedComments as comments_count'])
            ->when($category, fn ($query) => $query->where('article_category_id', $category->id))
            ->when($tag, fn ($query) => $query->whereHas('tags', fn ($tags) => $tags->whereKey($tag->id)))
            ->when($author, fn ($query) => $query->where('team_member_id', $author->id))
            ->when($search !== '', fn ($query) => $query->where(fn ($searchQuery) => $searchQuery
                ->where('title', 'like', '%'.$search.'%')
                ->orWhere('excerpt', 'like', '%'.$search.'%')
                ->orWhere('content', 'like', '%'.$search.'%')
                ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('tags', fn ($tagQuery) => $tagQuery->where('name', 'like', '%'.$search.'%'))));

        $articles = $articlesQuery->latest('published_at')->paginate(12)->withQueryString();
        $featured = $search === '' && ! $category && ! $tag && ! $author
            ? Article::featured()->with(['category', 'authorMember'])->withCount(['reactions as likes_count', 'approvedComments as comments_count'])->limit(1)->first()
            : null;
        $trending = $search === '' && ! $category && ! $tag && ! $author
            ? Article::published()->with(['category', 'authorMember'])->withCount(['reactions as likes_count', 'approvedComments as comments_count'])->orderByDesc('likes_count')->orderByDesc('comments_count')->latest('published_at')->limit(8)->get()->filter(fn ($article) => ($article->likes_count + $article->comments_count) > 0)->take(3)->values()
            : collect();

        return view('public.journal.index', compact('articles', 'categories', 'featured', 'trending', 'category', 'tag', 'author', 'search'))->with('page', $this->cmsPage('journal'));
    }
}
