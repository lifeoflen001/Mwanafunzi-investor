<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BusinessUnit;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseFaq;
use App\Models\Faq;
use App\Models\LearningTopic;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Service;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Rules\Recaptcha;
use App\Support\PublicHero;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class PublicSiteController extends Controller
{
    public function module(string $slug)
    {
        $module = BusinessUnit::query()->active()->where('slug', $slug)->firstOrFail();
        $page = $this->cmsPage($slug);
        $serviceScope = fn ($query) => $query->where('business_unit_id', $module->id);
        $featuredProjects = Project::published()->whereHas('services', $serviceScope)->with(['services', 'gallery'])->orderBy('display_order')->orderByDesc('project_date')->limit(6)->get();
        $industries = Project::published()->whereHas('services', $serviceScope)->whereNotNull('industry')->where('industry', '<>', '')->distinct()->orderBy('industry')->pluck('industry');
        $featuredTestimonials = Testimonial::published()->where(function ($query) use ($module, $serviceScope) {
            $query->where('business_unit_id', $module->id)->orWhereHas('project.services', $serviceScope);
        })->with(['project', 'businessUnit'])->orderBy('sort_order')->limit(6)->get();
        PublicHero::preloadMedia($featuredProjects->pluck('featured_image'));
        $this->preloadTestimonialMedia($featuredTestimonials);

        if ($module->route_name === 'home') {
            return to_route('home');
        }

        if ($module->slug === 'development') {
            return view('public.modules.development', compact('module', 'page', 'featuredProjects', 'featuredTestimonials', 'industries'));
        }

        if ($module->slug === 'studio') {
            return view('public.modules.studio', compact('module', 'page', 'featuredProjects', 'featuredTestimonials'));
        }

        return view('public.modules.show', compact('module', 'page'));
    }

    public function moduleCollection(string $slug, string $collection)
    {
        abort_unless(in_array($collection, ['products', 'services', 'projects', 'testimonials'], true), 404);

        $module = BusinessUnit::query()->active()->where('slug', $slug)->firstOrFail();
        $page = $this->cmsPage($slug);
        $services = collect();
        $projects = collect();
        $testimonials = collect();
        $projectFilters = [
            'q' => trim((string) request('q')),
            'industry' => trim((string) request('industry')),
            'type' => trim((string) request('type')),
        ];
        $industries = collect();
        $projectTypes = collect();

        if ($collection === 'services') {
            $services = Service::active()->where('business_unit_id', $module->id)->with('projects')->orderBy('sort_order')->orderBy('title')->get();
            PublicHero::preloadMedia($services->pluck('featured_image'));
        }

        if ($collection === 'projects') {
            $projectQuery = Project::published()
                ->whereHas('services', fn ($query) => $query->where('business_unit_id', $module->id))
                ->with(['services', 'gallery'])
                ->when($projectFilters['q'], function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('title', 'like', '%'.$search.'%')
                            ->orWhere('short_description', 'like', '%'.$search.'%')
                            ->orWhere('industry', 'like', '%'.$search.'%')
                            ->orWhere('project_type', 'like', '%'.$search.'%');
                    });
                })
                ->when($projectFilters['industry'], fn ($query, string $industry) => $query->where('industry', $industry))
                ->when($projectFilters['type'], fn ($query, string $type) => $query->where('project_type', $type))
                ->orderBy('display_order')
                ->orderByDesc('project_date');

            $projects = $projectQuery->paginate(12)->withQueryString();
            $catalogueQuery = Project::published()->whereHas('services', fn ($query) => $query->where('business_unit_id', $module->id));
            $industries = (clone $catalogueQuery)->whereNotNull('industry')->where('industry', '<>', '')->distinct()->orderBy('industry')->pluck('industry');
            $projectTypes = (clone $catalogueQuery)->whereNotNull('project_type')->where('project_type', '<>', '')->distinct()->orderBy('project_type')->pluck('project_type');
            PublicHero::preloadMedia($projects->getCollection()->pluck('featured_image'));
        }

        if ($collection === 'testimonials') {
            $testimonials = Testimonial::published()->where(function ($query) use ($module) {
                $query->where('business_unit_id', $module->id)->orWhereHas('project.services', fn ($projectQuery) => $projectQuery->where('business_unit_id', $module->id));
            })->with(['project', 'businessUnit'])->orderBy('sort_order')->paginate(12);
            $this->preloadTestimonialMedia($testimonials->getCollection());
        }

        return view('public.modules.collection', compact('module', 'page', 'collection', 'services', 'projects', 'testimonials', 'projectFilters', 'industries', 'projectTypes'));
    }

    public function hospitality()
    {
        $module = BusinessUnit::query()->active()->where('slug', 'development')->firstOrFail();
        $page = $this->cmsPage('development');
        $terms = ['hospital', 'lodge', 'safari', 'tourism', 'tour operator', 'travel'];
        $projects = Project::published()
            ->whereHas('services', fn ($query) => $query->where('business_unit_id', $module->id))
            ->where(function ($query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('industry', 'like', '%'.$term.'%')->orWhere('project_type', 'like', '%'.$term.'%');
                }
            })
            ->with(['services', 'gallery'])
            ->orderBy('display_order')
            ->orderByDesc('project_date')
            ->paginate(9);
        PublicHero::preloadMedia($projects->getCollection()->pluck('featured_image'));

        return view('public.digital-software.hospitality', compact('module', 'page', 'projects'));
    }

    public function digitalProject(Project $project)
    {
        $module = BusinessUnit::query()->active()->where('slug', 'development')->firstOrFail();
        abort_unless($project->services()->where('business_unit_id', $module->id)->exists(), 404);
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);

        $project->load([
            'services.businessUnit',
            'gallery' => fn ($query) => $query->where('media.status', 'published'),
            'testimonials' => fn ($query) => $query->published()->orderBy('sort_order'),
        ]);
        $this->preloadTestimonialMedia($project->testimonials);
        PublicHero::preloadMedia([$project->featured_image]);

        return view('public.projects.digital-show', compact('project', 'module'));
    }

    public function home()
    {
        $homePage = $this->cmsPage('home');
        $featuredProjects = Project::published()->where('is_featured', true)->with(['services', 'gallery'])->orderBy('display_order')->limit(4)->get();
        $featuredServices = Service::active()->where('is_featured', true)->orderBy('sort_order')->limit(4)->get();
        $featuredTestimonials = Testimonial::published()->where('is_featured', true)->with('project')->orderBy('sort_order')->limit(8)->get();
        PublicHero::preloadMedia($featuredProjects->pluck('featured_image')->merge($featuredServices->pluck('featured_image')));
        $this->preloadTestimonialMedia($featuredTestimonials);

        return view('welcome', [
            'homePage' => $homePage,
            'products' => Product::available()->orderBy('sort_order')->limit(3)->get(),
            'learningTopics' => LearningTopic::query()->where('is_published', true)->orderBy('sort_order')->limit(8)->get(),
            'courses' => Course::published()->orderBy('sort_order')->limit(4)->get(),
            'featuredArticle' => Article::featured()->with('category')->first(),
            'featuredServices' => $featuredServices,
            'featuredProjects' => $featuredProjects,
            'featuredTestimonials' => $featuredTestimonials,
        ]);
    }

    public function services()
    {
        return view('public.services.index', [
            'businessUnits' => BusinessUnit::active()->orderBy('sort_order')->get(),
            'serviceRoutes' => ['forex' => route('forex-academy'), 'development' => route('digital-systems'), 'studio' => route('creative-studio')],
            'page' => $this->cmsPage('services'),
        ]);
    }

    public function service(Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load(['businessUnit', 'projects' => fn ($query) => $query->published()->with('services')->orderBy('display_order')]);
        PublicHero::preloadMedia([$service->featured_image, ...$service->projects->pluck('featured_image')->all()]);

        return view('public.services.show', ['service' => $service, 'relatedServices' => Service::active()->whereKeyNot($service->id)->when($service->business_unit_id, fn ($query, $businessUnitId) => $query->where('business_unit_id', $businessUnitId))->orderBy('sort_order')->limit(3)->get()]);
    }

    public function serviceForModule(Service $service)
    {
        $module = BusinessUnit::query()->active()->where('slug', request()->route('module_slug', 'studio'))->firstOrFail();
        abort_unless($service->is_active && $service->business_unit_id === $module->id, 404);
        $service->load(['businessUnit', 'projects' => fn ($query) => $query->published()->with('services')->orderBy('display_order')]);
        $relatedServices = Service::active()->where('business_unit_id', $module->id)->whereKeyNot($service->id)->orderBy('sort_order')->limit(3)->get();
        PublicHero::preloadMedia([$service->featured_image, ...$service->projects->pluck('featured_image')->all()]);

        return view('public.services.show', compact('service', 'relatedServices', 'module'));
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
        $project->load(['services.businessUnit', 'gallery' => fn ($query) => $query->where('media.status', 'published'), 'testimonials' => fn ($query) => $query->published()->orderBy('sort_order')]);
        $this->preloadTestimonialMedia($project->testimonials);
        $studioService = $project->services->first(fn ($service) => $service->businessUnit?->slug === 'studio');
        $isStudioProject = $studioService !== null;
        PublicHero::preloadMedia([$project->featured_image]);

        return view('public.projects.show', compact('project', 'isStudioProject', 'studioService'));
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
        $courses = Course::published()
            ->select(['id', 'title', 'slug', 'featured_image', 'level', 'status', 'short_description', 'sort_order'])
            ->withCount('modules')
            ->orderBy('sort_order')
            ->paginate(12);

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
            $article = Article::withTrashed()->with(['category', 'tags', 'socialLinks', 'authorMember', 'learningTopics'])->withCount(['reactions as likes_count', 'approvedComments as comments_count'])->findOrFail($id);

            return view('public.journal.show', ['article' => $article, 'relatedArticles' => collect(), 'comments' => collect(), 'userHasLiked' => false, 'previousArticle' => null, 'nextArticle' => null, 'preview' => true]);
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
            if ($redirect) {
                return redirect()->to($redirect->destination_path, $redirect->status_code);
            }
            abort(404);
        }
        abort_unless($page->status === 'published' && $page->is_visible && (! $page->published_at || $page->published_at->isPast()), 404);
        $page->load(['sections', 'faqs']);

        return view('public.pages.cms', compact('page'));
    }

    public function tools()
    {
        $products = Product::available()
            ->select(['id', 'name', 'slug', 'product_type', 'thumbnail', 'short_description', 'availability', 'sort_order'])
            ->with('features:id,product_id,title,description,sort_order')
            ->orderBy('sort_order')
            ->paginate(12);

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
        $article->load(['category', 'tags', 'socialLinks', 'authorMember', 'learningTopics' => fn ($query) => $query->where('is_published', true)->where('status', 'published')->orderBy('sort_order')]);
        $article->loadCount(['reactions as likes_count', 'approvedComments as comments_count']);
        $visibleComments = $article->comments()
            ->where(function ($query) {
                $query->where('status', 'approved');
                if (auth()->check()) {
                    $query->orWhere(fn ($pending) => $pending->where('status', 'pending')->where('user_id', auth()->id()));
                }
            })
            ->with(['user.socialIdentities', 'reactions' => fn ($query) => auth()->check() ? $query->where('user_id', auth()->id()) : $query->whereKey(0)])
            ->withCount(['upReactions', 'downReactions'])
            ->latest()
            ->get();
        $commentsByParent = $visibleComments->groupBy(fn (Comment $comment) => $comment->parent_id ?: 0);
        $comments = $this->commentTree($commentsByParent);
        $tagIds = $article->tags->modelKeys();
        $relatedArticles = Article::published()->with(['category', 'authorMember'])->withCount(['approvedComments as comments_count'])
            ->whereKeyNot($article->id)
            ->where(function ($query) use ($article, $tagIds) {
                if ($article->article_category_id) {
                    $query->where('article_category_id', $article->article_category_id);
                }
                if ($tagIds) {
                    $query->orWhereHas('tags', fn ($tags) => $tags->whereIn('tags.id', $tagIds));
                }
            })
            ->latest('published_at')->latest('id')->limit(3)->get();
        $previousArticle = Article::published()->where(function ($query) use ($article) {
            $query->where('published_at', '<', $article->published_at)->orWhere(fn ($sameDate) => $sameDate->where('published_at', $article->published_at)->where('id', '<', $article->id));
        })->latest('published_at')->latest('id')->first();
        $nextArticle = Article::published()->where(function ($query) use ($article) {
            $query->where('published_at', '>', $article->published_at)->orWhere(fn ($sameDate) => $sameDate->where('published_at', $article->published_at)->where('id', '>', $article->id));
        })->oldest('published_at')->oldest('id')->first();

        return view('public.journal.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'previousArticle' => $previousArticle,
            'nextArticle' => $nextArticle,
            'comments' => $comments,
            'userHasLiked' => auth()->check() && $article->reactions()->where('user_id', auth()->id())->where('type', 'like')->exists(),
        ]);
    }

    public function feed()
    {
        $articles = Article::published()->with(['category', 'authorMember'])->latest('published_at')->limit(30)->get();

        return response()->view('public.feed', compact('articles'))->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    private function preloadTestimonialMedia(Collection $testimonials): void
    {
        PublicHero::preloadMedia($testimonials->flatMap(fn (Testimonial $testimonial) => [$testimonial->client_photo, $testimonial->company_logo]));
    }

    private function commentTree(Collection $commentsByParent, int $parentId = 0): Collection
    {
        return $commentsByParent->get($parentId, collect())->map(function (Comment $comment) use ($commentsByParent) {
            $comment->setRelation('replies', $this->commentTree($commentsByParent, $comment->id));

            return $comment;
        });
    }

    public function contact()
    {
        $businessUnits = BusinessUnit::query()->active()->orderBy('sort_order')->get();
        $selectedBusinessUnit = $businessUnits->firstWhere('slug', request('module'));

        return view('public.contact', ['businessUnits' => $businessUnits, 'selectedBusinessUnit' => $selectedBusinessUnit, 'page' => $this->cmsPage('contact')]);
    }

    public function faqs()
    {
        $items = collect();

        CourseFaq::with('course')->orderBy('sort_order')->orderBy('id')->get()
            ->filter(fn (CourseFaq $faq): bool => $faq->course && in_array($faq->course->status, ['published', 'open', 'coming_soon'], true))
            ->each(fn (CourseFaq $faq) => $items->push([
                'target' => $faq->course->title,
                'type' => 'Course',
                'question' => $faq->question,
                'answer' => $faq->answer,
                'sort_order' => $faq->sort_order,
            ]));

        Faq::with('faqable')->orderBy('sort_order')->orderBy('id')->get()
            ->filter(function (Faq $faq): bool {
                $target = $faq->faqable;

                return match (true) {
                    $target instanceof Product => in_array($target->availability, ['available', 'coming_soon', 'waitlist'], true),
                    $target instanceof Page => $target->status === 'published'
                        && $target->is_visible
                        && (! $target->published_at || $target->published_at->isPast()),
                    $target instanceof LearningTopic => $target->is_published && $target->status === 'published',
                    default => false,
                };
            })
            ->each(function (Faq $faq) use ($items): void {
                $target = $faq->faqable;
                $items->push([
                    'target' => $target instanceof Product ? $target->name : ($target->title ?? $target->name),
                    'type' => match (true) {
                        $target instanceof Product => 'Digital tool',
                        $target instanceof LearningTopic => 'Learning path',
                        default => 'Platform',
                    },
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'sort_order' => $faq->sort_order,
                ]);
            });

        $groups = $items->sortBy([['target', 'asc'], ['sort_order', 'asc']])->groupBy('target');

        return view('public.faqs.index', ['groups' => $groups, 'page' => $this->cmsPage('faqs')]);
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
            'recaptcha_token' => Recaptcha::rules('contact'),
        ]);
        ContactMessage::create([...$validated, 'consented_at' => now()]);

        return to_route('contact')->with('success', 'Thank you. Your message has been received by the Mwanafunzi desk.');
    }

    public function page(string $page)
    {
        abort_unless(in_array($page, ['about', 'student-of-money', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer'], true), 404);
        $cmsPage = $this->cmsPage($page);
        $data = ['page' => $cmsPage];
        if ($page === 'about') {
            $data['teamMembers'] = $this->teamMembers();
        }

        return view("public.pages.{$page}", $data);
    }

    public function cookiePolicy()
    {
        return view('public.pages.cookie-policy', ['page' => $this->cmsPage('cookie-policy')]);
    }

    public function teamMember(string $member)
    {
        $member = TeamMember::active()->where('slug', $member)->firstOrFail();

        return view('public.team.show', compact('member'));
    }

    public function sitemap()
    {
        $entries = collect();
        $add = function (?string $url, mixed $lastModified = null) use (&$entries): void {
            if (blank($url)) {
                return;
            }

            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                $siteHost = strtolower((string) parse_url(Seo::siteUrl(), PHP_URL_HOST));
                if ($host && $siteHost && $host !== $siteHost) {
                    return;
                }
            }

            $path = Seo::routePath($url);
            $canonical = Seo::canonical($path);
            $entries->put($canonical, $lastModified?->toAtomString());
        };
        $isIndexable = fn ($record): bool => ! str_contains((string) ($record->robots ?? 'index,follow'), 'noindex');

        foreach ([
            ['home', route('home')], ['learn', route('forex-academy')], ['development', route('digital-systems')],
            ['studio', route('creative-studio')], ['services', route('services')], ['projects', route('projects')],
            ['learn-index', route('learn')], ['courses', route('courses')], ['tools', route('tools')],
            ['blog', route('blog')], ['faqs', route('faqs')], ['site-map', route('site-map')], ['about', route('about')], ['contact', route('contact')],
            ['student-of-money', route('student-of-money')], ['privacy-policy', route('legal', 'privacy-policy')],
            ['terms', route('legal', 'terms')], ['risk-disclosure', route('legal', 'risk-disclosure')],
            ['refund-policy', route('legal', 'refund-policy')], ['disclaimer', route('legal', 'disclaimer')],
            ['cookie-policy', route('cookie-policy')],
        ] as [$key, $url]) {
            $page = Page::published()->where('key', $key)->first();
            if (! $page || $isIndexable($page)) {
                $add($url, $page?->updated_at);
            }
        }

        // The legacy /studio route renders the same public page as /creative-studio.
        // Keep the canonical route in the sitemap and expose the legacy route only
        // through its canonical link.
        BusinessUnit::active()->whereNotNull('route_name')->whereNotIn('route_name', ['home', 'studio'])->get()->each(fn ($unit) => $add(route($unit->route_name), $unit->updated_at));
        TeamMember::active()->get()->filter($isIndexable)->each(fn ($member) => $add($member->canonical_url ?: route('team.show', $member->slug), $member->updated_at));
        LearningTopic::where('is_published', true)->where('status', 'published')->get()->filter($isIndexable)->each(fn ($topic) => $add($topic->canonical_url ?: route('learn.show', $topic), $topic->updated_at));
        Course::published()->get()->filter($isIndexable)->each(fn ($course) => $add($course->canonical_url ?: route('courses.show', $course), $course->updated_at));
        Product::available()->get()->filter($isIndexable)->each(fn ($product) => $add($product->canonical_url ?: route('tools.show', $product), $product->updated_at));
        Article::published()->get()->filter($isIndexable)->each(fn ($article) => $add($article->canonicalUrl(), $article->updated_at ?: $article->published_at));
        ArticleCategory::where('is_active', true)->whereHas('articles', fn ($query) => $query->published())->get()->each(fn ($category) => $add(route('blog.category', $category), $category->updated_at));
        Tag::whereHas('articles', fn ($query) => $query->published())->get()->each(fn ($tag) => $add(route('blog.tag', $tag), $tag->updated_at));
        TeamMember::active()->whereHas('articles', fn ($query) => $query->published())->get()->each(fn ($member) => $add(route('blog.author', $member), $member->updated_at));
        Service::active()->get()->filter($isIndexable)->each(fn ($service) => $add($service->canonical_url ?: route('services.show', $service), $service->updated_at));
        Project::published()->get()->filter($isIndexable)->each(fn ($project) => $add($project->canonical_url ?: route('projects.show', $project), $project->updated_at ?: $project->published_at));
        Page::published()->whereNotNull('slug')->whereNotIn('key', ['home', 'learn', 'courses', 'tools', 'journal', 'about', 'contact', 'student-of-money', 'development', 'studio', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer', 'cookie-policy'])->get()->filter($isIndexable)->each(fn ($page) => $add($page->canonical_url ?: route('pages.show', ['slug' => $page->slug]), $page->updated_at));

        $xml = view('seo.sitemap', ['entries' => $entries])->render();

        return response($xml)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function htmlSiteMap()
    {
        $groups = [
            'Explore' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Financial Academy', 'url' => route('forex-academy')],
                ['label' => 'Digital Software', 'url' => route('digital-systems')],
                ['label' => 'Creative Studio', 'url' => route('creative-studio')],
                ['label' => 'Services', 'url' => route('services')],
                ['label' => 'Projects', 'url' => route('projects')],
                ['label' => 'Blog', 'url' => route('blog')],
                ['label' => 'FAQs', 'url' => route('faqs')],
                ['label' => 'About', 'url' => route('about')],
                ['label' => 'Contact', 'url' => route('contact')],
            ],
            'Financial Academy' => [
                ['label' => 'Learn', 'url' => route('learn')],
                ['label' => 'Courses', 'url' => route('courses')],
                ['label' => 'Tools', 'url' => route('tools')],
            ],
            'Policies' => [
                ['label' => 'Privacy Policy', 'url' => route('legal', 'privacy-policy')],
                ['label' => 'Terms', 'url' => route('legal', 'terms')],
                ['label' => 'Risk Disclosure', 'url' => route('legal', 'risk-disclosure')],
                ['label' => 'Refund Policy', 'url' => route('legal', 'refund-policy')],
                ['label' => 'Disclaimer', 'url' => route('legal', 'disclaimer')],
                ['label' => 'Cookie Policy', 'url' => route('cookie-policy')],
            ],
        ];

        return view('seo.site-map', compact('groups'));
    }

    private function cmsPage(string $key): ?Page
    {
        $page = Page::where('key', $key)->first();
        if (! $page) {
            return null;
        }
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
        $search = mb_substr($search, 0, 120);
        $sort = in_array($request->input('sort'), ['oldest', 'updated'], true) ? $request->input('sort') : 'newest';
        $queryCategory = trim((string) $request->input('category'));
        $queryTag = trim((string) $request->input('tag'));
        if (! $category && $queryCategory !== '') {
            $category = ArticleCategory::where('slug', $queryCategory)->where('is_active', true)->firstOrFail();
        }
        if (! $tag && $queryTag !== '') {
            $tag = Tag::where('slug', $queryTag)->first();
        }
        if ($category) {
            abort_unless($category->is_active, 404);
        }
        $categories = ArticleCategory::where('is_active', true)
            ->whereHas('articles', fn ($query) => $query->published())
            ->withCount(['articles as published_articles_count' => fn ($query) => $query->published()])
            ->orderBy('sort_order')->orderBy('name')->get();
        $tags = Tag::whereHas('articles', fn ($query) => $query->published())->orderBy('name')->get();
        $articleListColumns = ['id', 'article_category_id', 'user_id', 'team_member_id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'og_image', 'author', 'published_at', 'updated_at', 'status'];
        $articlesQuery = Article::published()->select($articleListColumns)->with(['category', 'authorMember'])
            ->when($category, fn ($query) => $query->where('article_category_id', $category->id))
            ->when($tag, fn ($query) => $query->whereHas('tags', fn ($tags) => $tags->whereKey($tag->id)))
            ->when($author, fn ($query) => $query->where('team_member_id', $author->id))
            ->when($search !== '', fn ($query) => $query->where(fn ($searchQuery) => $searchQuery
                ->where('title', 'like', '%'.$search.'%')
                ->orWhere('excerpt', 'like', '%'.$search.'%')
                ->orWhere('content', 'like', '%'.$search.'%')
                ->orWhere('content_type', 'like', '%'.$search.'%')
                ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('tags', fn ($tagQuery) => $tagQuery->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('authorMember', fn ($authorQuery) => $authorQuery->where('name', 'like', '%'.$search.'%'))));

        if ($sort === 'oldest') {
            $articlesQuery->orderBy('published_at')->orderBy('id');
        } elseif ($sort === 'updated') {
            $articlesQuery->orderByDesc('updated_at')->orderByDesc('id');
        } else {
            $articlesQuery->latest('published_at')->latest('id');
        }

        $articles = $articlesQuery->paginate(12)->withQueryString();
        $view = $request->input('view') === 'list' ? 'list' : 'grid';
        $hasDiscoveryQuery = $search !== '' || $sort !== 'newest' || $request->filled('view') || $queryCategory !== '' || $queryTag !== '';

        return view('public.journal.index', compact('articles', 'categories', 'tags', 'category', 'tag', 'author', 'search', 'sort', 'view', 'hasDiscoveryQuery'))->with('page', $this->cmsPage('journal'));
    }
}
