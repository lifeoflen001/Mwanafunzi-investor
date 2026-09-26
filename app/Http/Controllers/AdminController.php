<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\ArticleCategory;
use App\Models\LearningTopic;
use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\ProductVersion;
use App\Models\ProductImage;
use App\Models\Media;
use App\Models\Page;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\CourseFaq;
use App\Models\Faq;
use App\Services\MediaService;
use App\Support\RichText;
use App\Support\AdminAudit;
use App\Support\HeroFocalPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'counts' => ['published pages' => Page::published()->count(), 'published courses' => Course::where('status', 'published')->count(), 'products' => Product::count(), 'articles' => Article::count(), 'media assets' => Media::count(), 'students' => User::where('is_admin', false)->count(), 'administrators' => User::where('is_admin', true)->count(), 'orders' => Order::count(), 'unread messages' => ContactMessage::whereNull('read_at')->count()],
            'recentMessages' => ContactMessage::latest()->limit(6)->get(),
            'recentContent' => collect([
                ...Article::latest()->limit(3)->get()->map(fn ($item) => ['type' => 'Article', 'title' => $item->title, 'date' => $item->created_at]),
                ...Course::latest()->limit(3)->get()->map(fn ($item) => ['type' => 'Course', 'title' => $item->title, 'date' => $item->created_at]),
            ])->sortByDesc('date')->take(6),
            'mediaStorage' => Media::sum('size'),
        ]);
    }

    public function courses(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', 'in:published,draft,coming_soon']]);
        $courses = Course::withTrashed()->withCount('modules')->when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();
        return view('admin.courses.index', compact('courses'));
    }
    public function courseCreate() { return view('admin.courses.form', ['course' => new Course(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.courses.store')]); }
    public function courseStore(Request $request) { return $this->saveCourse($request, new Course()); }
    public function courseEdit(Course $course) { return view('admin.courses.form', ['course' => $course->load(['modules' => fn ($query) => $query->withTrashed(), 'modules.lessons' => fn ($query) => $query->withTrashed(), 'faqs']), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.courses.update', $course)]); }
    public function courseUpdate(Request $request, Course $course) { return $this->saveCourse($request, $course); }
    public function courseDestroy(Course $course) { $course->delete(); AdminAudit::record('course.archived', 'Archived course '.$course->title, $course); return back()->with('success', 'Course archived.'); }
    public function courseRestore(int $course) { $record = Course::withTrashed()->findOrFail($course); $record->restore(); AdminAudit::record('course.restored', 'Restored course '.$record->title, $record); return back()->with('success', 'Course restored.'); }
    public function moduleStore(Request $request, Course $course)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'description' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $module = $course->modules()->create($data);
        AdminAudit::record('course.module.created', 'Added module '.$module->title, $course);
        return back()->with('success', 'Course module added.');
    }
    public function moduleUpdate(Request $request, CourseModule $module)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'description' => ['nullable', 'string', 'max:2000'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $module->update($data);
        AdminAudit::record('course.module.updated', 'Updated module '.$module->title, $module);
        return back()->with('success', 'Course module updated.');
    }
    public function moduleDestroy(CourseModule $module) { $module->delete(); AdminAudit::record('course.module.deleted', 'Removed course module '.$module->title, $module); return back()->with('success', 'Course module removed.'); }
    public function moduleRestore(int $module) { $record = CourseModule::withTrashed()->findOrFail($module); $record->restore(); AdminAudit::record('course.module.restored', 'Restored course module '.$record->title, $record); return back()->with('success', 'Course module restored.'); }
    public function lessonStore(Request $request, CourseModule $module)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'content' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['content'] = RichText::sanitize($data['content'] ?? null);
        $lesson = $module->lessons()->create($data);
        AdminAudit::record('course.lesson.created', 'Added lesson '.$lesson->title, $module);
        return back()->with('success', 'Course lesson added.');
    }
    public function lessonUpdate(Request $request, CourseLesson $lesson)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'content' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['content'] = RichText::sanitize($data['content'] ?? null);
        $lesson->update($data);
        AdminAudit::record('course.lesson.updated', 'Updated lesson '.$lesson->title, $lesson);
        return back()->with('success', 'Course lesson updated.');
    }
    public function lessonDestroy(CourseLesson $lesson) { $lesson->delete(); AdminAudit::record('course.lesson.deleted', 'Removed course lesson '.$lesson->title, $lesson); return back()->with('success', 'Course lesson removed.'); }
    public function lessonRestore(int $lesson) { $record = CourseLesson::withTrashed()->findOrFail($lesson); $record->restore(); AdminAudit::record('course.lesson.restored', 'Restored course lesson '.$record->title, $record); return back()->with('success', 'Course lesson restored.'); }
    public function courseFaqStore(Request $request, Course $course)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $faq = $course->faqs()->create($data);
        AdminAudit::record('course.faq.created', 'Added course FAQ', $faq);
        return back()->with('success', 'Course FAQ added.');
    }
    public function courseFaqDestroy(CourseFaq $faq) { $faq->delete(); AdminAudit::record('course.faq.deleted', 'Removed course FAQ', $faq); return back()->with('success', 'Course FAQ removed.'); }

    public function products(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'availability' => ['nullable', 'in:available,waitlist,coming_soon']]);
        $products = Product::withTrashed()->when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->when($data['availability'] ?? null, fn ($query, $availability) => $query->where('availability', $availability))->latest()->paginate(20)->withQueryString();
        return view('admin.products.index', compact('products'));
    }
    public function productCreate() { return view('admin.products.form', ['product' => new Product(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.products.store')]); }
    public function productStore(Request $request) { return $this->saveProduct($request, new Product()); }
    public function productEdit(Product $product) { return view('admin.products.form', ['product' => $product->load(['features', 'versions', 'faqs', 'images', 'assets']), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.products.update', $product)]); }
    public function productUpdate(Request $request, Product $product) { return $this->saveProduct($request, $product); }
    public function productDestroy(Product $product) { $product->delete(); AdminAudit::record('product.archived', 'Archived product '.$product->name, $product); return back()->with('success', 'Product archived.'); }
    public function productRestore(int $product) { $record = Product::withTrashed()->findOrFail($product); $record->restore(); AdminAudit::record('product.restored', 'Restored product '.$record->name, $record); return back()->with('success', 'Product restored.'); }
    public function featureStore(Request $request, Product $product)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'description' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $feature = $product->features()->create($data);
        AdminAudit::record('product.feature.created', 'Added product feature '.$feature->title, $product);
        return back()->with('success', 'Product feature added.');
    }
    public function featureDestroy(ProductFeature $feature) { $feature->delete(); AdminAudit::record('product.feature.deleted', 'Removed product feature', $feature); return back()->with('success', 'Product feature removed.'); }
    public function versionStore(Request $request, Product $product)
    {
        $data = $request->validate(['version' => ['required', 'string', 'max:40'], 'released_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string']]);
        $version = $product->versions()->create($data);
        AdminAudit::record('product.version.created', 'Added product version '.$version->version, $product);
        return back()->with('success', 'Product version added.');
    }
    public function versionDestroy(ProductVersion $version) { $version->delete(); AdminAudit::record('product.version.deleted', 'Removed product version', $version); return back()->with('success', 'Product version removed.'); }
    public function productFaqStore(Request $request, Product $product)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $faq = $product->faqs()->create($data);
        AdminAudit::record('product.faq.created', 'Added product FAQ', $faq);
        return back()->with('success', 'Product FAQ added.');
    }
    public function productFaqDestroy(Faq $faq) { $faq->delete(); AdminAudit::record('product.faq.deleted', 'Removed product FAQ', $faq); return back()->with('success', 'Product FAQ removed.'); }
    public function productImageStore(Request $request, Product $product, MediaService $mediaService)
    {
        $data = $request->validate(['file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'], 'existing_path' => ['nullable', 'string', 'max:255'], 'alt_text' => ['required', 'string', 'max:190']]);
        if (! $request->hasFile('file') && empty($data['existing_path'])) return back()->withErrors(['file' => 'Choose an existing media item or upload a new image.']);
        if (! empty($data['existing_path'])) {
            abort_unless(Media::where('path', $data['existing_path'])->exists(), 422);
            $path = $data['existing_path'];
        } else {
            $path = $mediaService->store($request->file('file'), 'products', $data['alt_text'])->path;
        }
        $image = $product->images()->create(['path' => $path, 'alt_text' => $data['alt_text'], 'sort_order' => $product->images()->max('sort_order') + 1]);
        AdminAudit::record('product.image.created', 'Added product screenshot', $image);
        return back()->with('success', 'Product screenshot added.');
    }
    public function productImageDestroy(ProductImage $image)
    {
        $media = Media::where('path', $image->path)->first();
        $isShared = ProductImage::where('path', $image->path)->where('id', '!=', $image->id)->exists();
        if ($media && ! $isShared) app(MediaService::class)->delete($media);
        elseif (! $media && ! $isShared) Storage::disk('public')->delete($image->path);
        $image->delete();
        AdminAudit::record('product.image.deleted', 'Removed product screenshot', $image);
        return back()->with('success', 'Product screenshot removed.');
    }

    public function articles(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', 'in:draft,published']]);
        $articles = Article::withTrashed()->with('category')->when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();
        return view('admin.articles.index', compact('articles'));
    }
    public function articleCreate() { return view('admin.articles.form', ['article' => new Article(), 'categories' => ArticleCategory::orderBy('name')->get(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.articles.store')]); }
    public function articleStore(Request $request) { return $this->saveArticle($request, new Article()); }
    public function articleEdit(Article $article) { return view('admin.articles.form', ['article' => $article->load('tags'), 'categories' => ArticleCategory::orderBy('name')->get(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.articles.update', $article)]); }
    public function articleUpdate(Request $request, Article $article) { return $this->saveArticle($request, $article); }
    public function articleDestroy(Article $article) { $article->delete(); AdminAudit::record('article.archived', 'Archived article '.$article->title, $article); return back()->with('success', 'Article archived.'); }
    public function articleRestore(int $article) { $record = Article::withTrashed()->findOrFail($article); $record->restore(); AdminAudit::record('article.restored', 'Restored article '.$record->title, $record); return back()->with('success', 'Article restored.'); }

    public function messages(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:new,in_progress,resolved,spam'],
        ]);
        $term = trim((string) ($data['q'] ?? ''));
        $messages = ContactMessage::with('businessUnit')
            ->when($term !== '', fn ($query) => $query->where(fn ($search) => $search
                ->where('name', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')
                ->orWhere('phone', 'like', '%'.$term.'%')
                ->orWhere('message', 'like', '%'.$term.'%')))
            ->when(! empty($data['status']), fn ($query) => $query->where('status', $data['status']))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.messages.index', compact('messages', 'term'));
    }
    public function messageShow(ContactMessage $message)
    {
        $message->load('businessUnit');
        if (! $message->read_at) $message->update(['read_at' => now()]);
        return view('admin.messages.show', compact('message'));
    }
    public function messageUpdate(Request $request, ContactMessage $message)
    {
        $data = $request->validate(['status' => ['required', 'in:new,in_progress,resolved,spam']]);
        $message->update(['status' => $data['status'], 'read_at' => $message->read_at ?: now()]);
        AdminAudit::record('message.status_updated', 'Updated enquiry status for '.$message->email, $message, ['status' => $data['status']]);
        return back()->with('success', 'Message status updated.');
    }
    public function messageRead(ContactMessage $message)
    {
        $message->update(['read_at' => now()]);
        AdminAudit::record('message.read', 'Marked enquiry as read for '.$message->email, $message);
        return back()->with('success', 'Message marked as read.');
    }
    public function topics(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $topics = LearningTopic::when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('slug', 'like', '%'.$term.'%')))->orderBy('sort_order')->paginate(20)->withQueryString();
        return view('admin.topics.index', compact('topics'));
    }
    public function topicCreate() { return view('admin.topics.form', ['topic' => new LearningTopic(['sort_order' => 0]), 'courses' => Course::orderBy('sort_order')->orderBy('title')->get(), 'articles' => Article::orderByDesc('created_at')->get(), 'products' => Product::orderBy('sort_order')->orderBy('name')->get(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.topics.store'), 'isCreate' => true]); }
    public function topicStore(Request $request)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title')), 'hero_focal_point' => $request->input('hero_focal_point', HeroFocalPoint::DEFAULT), 'hero_overlay' => $request->input('hero_overlay', 'medium'), 'hero_alignment' => $request->input('hero_alignment', 'left')]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('learning_topics', 'slug')], 'image' => ['nullable', 'string', 'max:255'], 'hero_eyebrow' => ['nullable', 'string', 'max:190'], 'hero_title' => ['nullable', 'string', 'max:190'], 'hero_summary' => ['nullable', 'string', 'max:1000'], 'hero_image' => ['nullable', 'string', 'max:255'], 'hero_focal_point' => ['required', Rule::in(HeroFocalPoint::options())], 'hero_overlay' => ['required', 'in:light,medium,strong'], 'hero_alignment' => ['required', 'in:left,center,right'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'learning_outcomes' => ['nullable', 'string', 'max:10000'], 'course_ids' => ['nullable', 'array'], 'course_ids.*' => ['integer', 'exists:courses,id'], 'article_ids' => ['nullable', 'array'], 'article_ids.*' => ['integer', 'exists:articles,id'], 'product_ids' => ['nullable', 'array'], 'product_ids.*' => ['integer', 'exists:products,id'], 'skill_level' => ['nullable', 'string', 'max:80'], 'study_time' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['is_published'] = $request->boolean('is_published');
        $data['status'] = $data['is_published'] ? 'published' : 'draft';
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        $data['learning_outcomes'] = collect(preg_split('/\r\n|\r|\n/', (string) ($data['learning_outcomes'] ?? '')))->map(fn ($item) => trim($item))->filter()->values()->all();
        $courseIds = $data['course_ids'] ?? [];
        $articleIds = $data['article_ids'] ?? [];
        $productIds = $data['product_ids'] ?? [];
        unset($data['course_ids'], $data['article_ids'], $data['product_ids']);
        $topic = LearningTopic::create($data);
        $topic->courses()->sync($courseIds);
        $topic->articles()->sync($articleIds);
        $topic->products()->sync($productIds);
        AdminAudit::record('topic.created', 'Created learning topic '.$topic->title, $topic);
        return to_route('admin.topics')->with('success', 'Learning topic created.');
    }
    public function topicEdit(LearningTopic $topic) { return view('admin.topics.form', ['topic' => $topic->load(['courses', 'articles', 'products']), 'courses' => Course::orderBy('sort_order')->orderBy('title')->get(), 'articles' => Article::orderByDesc('created_at')->get(), 'products' => Product::orderBy('sort_order')->orderBy('name')->get(), 'media' => Media::latest()->limit(60)->get(), 'action' => route('admin.topics.update', $topic), 'isCreate' => false]); }
    public function topicUpdate(Request $request, LearningTopic $topic)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title')), 'hero_focal_point' => $request->input('hero_focal_point', $topic->hero_focal_point ?: HeroFocalPoint::DEFAULT), 'hero_overlay' => $request->input('hero_overlay', $topic->hero_overlay ?: 'medium'), 'hero_alignment' => $request->input('hero_alignment', $topic->hero_alignment ?: 'left')]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('learning_topics', 'slug')->ignore($topic->id)], 'image' => ['nullable', 'string', 'max:255'], 'hero_eyebrow' => ['nullable', 'string', 'max:190'], 'hero_title' => ['nullable', 'string', 'max:190'], 'hero_summary' => ['nullable', 'string', 'max:1000'], 'hero_image' => ['nullable', 'string', 'max:255'], 'hero_focal_point' => ['required', Rule::in(HeroFocalPoint::options())], 'hero_overlay' => ['required', 'in:light,medium,strong'], 'hero_alignment' => ['required', 'in:left,center,right'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'learning_outcomes' => ['nullable', 'string', 'max:10000'], 'course_ids' => ['nullable', 'array'], 'course_ids.*' => ['integer', 'exists:courses,id'], 'article_ids' => ['nullable', 'array'], 'article_ids.*' => ['integer', 'exists:articles,id'], 'product_ids' => ['nullable', 'array'], 'product_ids.*' => ['integer', 'exists:products,id'], 'skill_level' => ['nullable', 'string', 'max:80'], 'study_time' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['is_published'] = $request->boolean('is_published');
        $data['status'] = $data['is_published'] ? 'published' : 'draft';
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        $data['learning_outcomes'] = collect(preg_split('/\r\n|\r|\n/', (string) ($data['learning_outcomes'] ?? '')))->map(fn ($item) => trim($item))->filter()->values()->all();
        $courseIds = $data['course_ids'] ?? [];
        $articleIds = $data['article_ids'] ?? [];
        $productIds = $data['product_ids'] ?? [];
        unset($data['course_ids'], $data['article_ids'], $data['product_ids']);
        $topic->update($data);
        $topic->courses()->sync($courseIds);
        $topic->articles()->sync($articleIds);
        $topic->products()->sync($productIds);
        AdminAudit::record('topic.updated', 'Updated learning topic '.$topic->title, $topic);
        return to_route('admin.topics')->with('success', 'Learning topic updated.');
    }

    private function saveCourse(Request $request, Course $course)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title')), 'hero_focal_point' => $request->input('hero_focal_point', HeroFocalPoint::DEFAULT)]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('courses', 'slug')->ignore($course->id)], 'featured_image' => ['nullable', 'string', 'max:255'], 'hero_focal_point' => ['required', Rule::in(HeroFocalPoint::options())], 'level' => ['nullable', 'string', 'max:80'], 'duration' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'instructor' => ['nullable', 'string', 'max:120'], 'status' => ['required', 'in:draft,published,open,closed,coming_soon,archived'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'cta_label' => ['nullable', 'string', 'max:120'], 'enrollment_available' => ['nullable', 'boolean'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['enrollment_available'] = $request->boolean('enrollment_available');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        $wasExisting = $course->exists;
        $course->fill($data)->save();
        AdminAudit::record($wasExisting ? 'course.updated' : 'course.created', ($wasExisting ? 'Updated course ' : 'Created course ').$course->title, $course);
        return to_route('admin.courses')->with('success', 'Course saved.');
    }

    private function saveProduct(Request $request, Product $product)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('name')), 'hero_focal_point' => $request->input('hero_focal_point', HeroFocalPoint::DEFAULT)]);
        $data = $request->validate(['name' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('products', 'slug')->ignore($product->id)], 'thumbnail' => ['nullable', 'string', 'max:255'], 'hero_focal_point' => ['required', Rule::in(HeroFocalPoint::options())], 'product_type' => ['nullable', 'string', 'max:100'], 'availability' => ['required', 'in:draft,available,coming_soon,waitlist,unavailable,archived'], 'short_description' => ['required', 'string'], 'detailed_description' => ['nullable', 'string'], 'version' => ['nullable', 'string', 'max:40'], 'price' => ['nullable', 'numeric', 'min:0'], 'sale_price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'download_format' => ['nullable', 'string', 'max:100'], 'system_requirements' => ['nullable', 'string'], 'demo_url' => ['nullable', 'url'], 'purchase_url' => ['nullable', 'url'], 'documentation_url' => ['nullable', 'url'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['detailed_description'] = RichText::sanitize($data['detailed_description'] ?? null);
        $wasExisting = $product->exists;
        $product->fill($data)->save();
        AdminAudit::record($wasExisting ? 'product.updated' : 'product.created', ($wasExisting ? 'Updated product ' : 'Created product ').$product->name, $product);
        return to_route('admin.products')->with('success', 'Product saved.');
    }

    private function saveArticle(Request $request, Article $article)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title')), 'hero_focal_point' => $request->input('hero_focal_point', HeroFocalPoint::DEFAULT)]);
        $data = $request->validate(['article_category_id' => ['nullable', 'exists:article_categories,id'], 'featured_image' => ['nullable', 'string', 'max:255'], 'hero_focal_point' => ['required', Rule::in(HeroFocalPoint::options())], 'title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('articles', 'slug')->ignore($article->id)], 'excerpt' => ['nullable', 'string'], 'content' => ['required', 'string'], 'author' => ['nullable', 'string', 'max:120'], 'reading_time' => ['nullable', 'integer', 'min:1', 'max:240'], 'status' => ['required', 'in:draft,published,scheduled,archived'], 'published_at' => ['nullable', 'date'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'canonical_url' => ['nullable', 'url', 'max:500'], 'robots' => ['nullable', 'regex:/^(index|noindex),(follow|nofollow)$/'], 'og_title' => ['nullable', 'string', 'max:190'], 'og_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255'], 'tags' => ['nullable', 'string', 'max:500']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        if (empty($data['reading_time'])) $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($data['content'])) / 200));
        $data['content'] = RichText::sanitize($data['content']);
        $tagNames = collect(explode(',', (string) ($data['tags'] ?? '')))->map(fn ($tag) => trim($tag))->filter()->unique()->values();
        unset($data['tags']);
        $wasExisting = $article->exists;
        $article->fill($data)->save();
        $tagIds = $tagNames->map(fn ($name) => \App\Models\Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);
        $article->tags()->sync($tagIds);
        AdminAudit::record($wasExisting ? 'article.updated' : 'article.created', ($wasExisting ? 'Updated article ' : 'Created article ').$article->title, $article);
        return to_route('admin.articles')->with('success', 'Article saved.');
    }
}
