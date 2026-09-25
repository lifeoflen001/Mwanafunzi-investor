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
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'counts' => ['published pages' => Page::published()->count(), 'published courses' => Course::where('status', 'published')->count(), 'products' => Product::count(), 'articles' => Article::count(), 'media assets' => Media::count(), 'students' => User::where('is_admin', false)->count(), 'orders' => Order::count(), 'unread messages' => ContactMessage::whereNull('read_at')->count()],
            'recentMessages' => ContactMessage::latest()->limit(6)->get(),
            'recentContent' => collect([
                ...Article::latest()->limit(3)->get()->map(fn ($item) => ['type' => 'Article', 'title' => $item->title, 'date' => $item->created_at]),
                ...Course::latest()->limit(3)->get()->map(fn ($item) => ['type' => 'Course', 'title' => $item->title, 'date' => $item->created_at]),
            ])->sortByDesc('date')->take(6),
            'mediaStorage' => Media::sum('size'),
        ]);
    }

    public function courses() { return view('admin.courses.index', ['courses' => Course::withTrashed()->latest()->paginate(20)]); }
    public function courseCreate() { return view('admin.courses.form', ['course' => new Course(), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.courses.store')]); }
    public function courseStore(Request $request) { return $this->saveCourse($request, new Course()); }
    public function courseEdit(Course $course) { return view('admin.courses.form', ['course' => $course->load(['modules.lessons', 'faqs']), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.courses.update', $course)]); }
    public function courseUpdate(Request $request, Course $course) { return $this->saveCourse($request, $course); }
    public function courseDestroy(Course $course) { $course->delete(); return back()->with('success', 'Course archived.'); }
    public function courseRestore(int $course) { Course::withTrashed()->findOrFail($course)->restore(); return back()->with('success', 'Course restored.'); }
    public function moduleStore(Request $request, Course $course)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'description' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $course->modules()->create($data);
        return back()->with('success', 'Course module added.');
    }
    public function moduleDestroy(CourseModule $module) { $module->delete(); return back()->with('success', 'Course module removed.'); }
    public function lessonStore(Request $request, CourseModule $module)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'content' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['content'] = RichText::sanitize($data['content'] ?? null);
        $module->lessons()->create($data);
        return back()->with('success', 'Course lesson added.');
    }
    public function lessonDestroy(CourseLesson $lesson) { $lesson->delete(); return back()->with('success', 'Course lesson removed.'); }
    public function courseFaqStore(Request $request, Course $course)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $course->faqs()->create($data);
        return back()->with('success', 'Course FAQ added.');
    }
    public function courseFaqDestroy(CourseFaq $faq) { $faq->delete(); return back()->with('success', 'Course FAQ removed.'); }

    public function products() { return view('admin.products.index', ['products' => Product::withTrashed()->latest()->paginate(20)]); }
    public function productCreate() { return view('admin.products.form', ['product' => new Product(), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.products.store')]); }
    public function productStore(Request $request) { return $this->saveProduct($request, new Product()); }
    public function productEdit(Product $product) { return view('admin.products.form', ['product' => $product->load(['features', 'versions', 'faqs', 'images', 'assets']), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.products.update', $product)]); }
    public function productUpdate(Request $request, Product $product) { return $this->saveProduct($request, $product); }
    public function productDestroy(Product $product) { $product->delete(); return back()->with('success', 'Product archived.'); }
    public function productRestore(int $product) { Product::withTrashed()->findOrFail($product)->restore(); return back()->with('success', 'Product restored.'); }
    public function featureStore(Request $request, Product $product)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'description' => ['nullable', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $product->features()->create($data);
        return back()->with('success', 'Product feature added.');
    }
    public function featureDestroy(ProductFeature $feature) { $feature->delete(); return back()->with('success', 'Product feature removed.'); }
    public function versionStore(Request $request, Product $product)
    {
        $data = $request->validate(['version' => ['required', 'string', 'max:40'], 'released_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string']]);
        $product->versions()->create($data);
        return back()->with('success', 'Product version added.');
    }
    public function versionDestroy(ProductVersion $version) { $version->delete(); return back()->with('success', 'Product version removed.'); }
    public function productFaqStore(Request $request, Product $product)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $product->faqs()->create($data);
        return back()->with('success', 'Product FAQ added.');
    }
    public function productFaqDestroy(Faq $faq) { $faq->delete(); return back()->with('success', 'Product FAQ removed.'); }
    public function productImageStore(Request $request, Product $product, MediaService $mediaService)
    {
        $data = $request->validate(['file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,avif,svg', 'max:8192'], 'existing_path' => ['nullable', 'string', 'max:255'], 'alt_text' => ['required', 'string', 'max:190']]);
        if (! $request->hasFile('file') && empty($data['existing_path'])) return back()->withErrors(['file' => 'Choose an existing media item or upload a new image.']);
        if (! empty($data['existing_path'])) {
            abort_unless(Media::where('path', $data['existing_path'])->exists(), 422);
            $path = $data['existing_path'];
        } else {
            $path = $mediaService->store($request->file('file'), 'products', $data['alt_text'])->path;
        }
        $product->images()->create(['path' => $path, 'alt_text' => $data['alt_text'], 'sort_order' => $product->images()->max('sort_order') + 1]);
        return back()->with('success', 'Product screenshot added.');
    }
    public function productImageDestroy(ProductImage $image)
    {
        $media = Media::where('path', $image->path)->first();
        $isShared = ProductImage::where('path', $image->path)->where('id', '!=', $image->id)->exists();
        if ($media && ! $isShared) app(MediaService::class)->delete($media);
        elseif (! $media && ! $isShared) Storage::disk('public')->delete($image->path);
        $image->delete();
        return back()->with('success', 'Product screenshot removed.');
    }

    public function articles() { return view('admin.articles.index', ['articles' => Article::withTrashed()->with('category')->latest()->paginate(20)]); }
    public function articleCreate() { return view('admin.articles.form', ['article' => new Article(), 'categories' => ArticleCategory::orderBy('name')->get(), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.articles.store')]); }
    public function articleStore(Request $request) { return $this->saveArticle($request, new Article()); }
    public function articleEdit(Article $article) { return view('admin.articles.form', ['article' => $article->load('tags'), 'categories' => ArticleCategory::orderBy('name')->get(), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.articles.update', $article)]); }
    public function articleUpdate(Request $request, Article $article) { return $this->saveArticle($request, $article); }
    public function articleDestroy(Article $article) { $article->delete(); return back()->with('success', 'Article archived.'); }
    public function articleRestore(int $article) { Article::withTrashed()->findOrFail($article)->restore(); return back()->with('success', 'Article restored.'); }

    public function messages() { return view('admin.messages.index', ['messages' => ContactMessage::with('businessUnit')->latest()->paginate(30)]); }
    public function messageUpdate(Request $request, ContactMessage $message)
    {
        $data = $request->validate(['status' => ['required', 'in:new,in_progress,resolved,spam']]);
        $message->update(['status' => $data['status'], 'read_at' => $message->read_at ?: now()]);
        return back()->with('success', 'Message status updated.');
    }
    public function messageRead(ContactMessage $message)
    {
        $message->update(['read_at' => now()]);
        return back()->with('success', 'Message marked as read.');
    }
    public function topics() { return view('admin.topics.index', ['topics' => LearningTopic::orderBy('sort_order')->paginate(20)]); }
    public function topicCreate() { return view('admin.topics.form', ['topic' => new LearningTopic(['sort_order' => 0]), 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.topics.store'), 'isCreate' => true]); }
    public function topicStore(Request $request)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title'))]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('learning_topics', 'slug')], 'image' => ['nullable', 'string', 'max:255'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'skill_level' => ['nullable', 'string', 'max:80'], 'study_time' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['is_published'] = $request->boolean('is_published');
        $data['status'] = $data['is_published'] ? 'published' : 'draft';
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        LearningTopic::create($data);
        return to_route('admin.topics')->with('success', 'Learning topic created.');
    }
    public function topicEdit(LearningTopic $topic) { return view('admin.topics.form', ['topic' => $topic, 'media' => Media::orderBy('filename')->get(), 'action' => route('admin.topics.update', $topic), 'isCreate' => false]); }
    public function topicUpdate(Request $request, LearningTopic $topic)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title'))]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('learning_topics', 'slug')->ignore($topic->id)], 'image' => ['nullable', 'string', 'max:255'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'skill_level' => ['nullable', 'string', 'max:80'], 'study_time' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'sort_order' => ['required', 'integer', 'min:0'], 'is_published' => ['nullable', 'boolean']]);
        $data['is_published'] = $request->boolean('is_published');
        $data['status'] = $data['is_published'] ? 'published' : 'draft';
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        $topic->update($data);
        return to_route('admin.topics')->with('success', 'Learning topic updated.');
    }

    private function saveCourse(Request $request, Course $course)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title'))]);
        $data = $request->validate(['title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('courses', 'slug')->ignore($course->id)], 'featured_image' => ['nullable', 'string', 'max:255'], 'level' => ['nullable', 'string', 'max:80'], 'duration' => ['nullable', 'string', 'max:80'], 'expected_availability' => ['nullable', 'string', 'max:120'], 'price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'instructor' => ['nullable', 'string', 'max:120'], 'status' => ['required', 'in:draft,published,open,closed,coming_soon,archived'], 'short_description' => ['required', 'string'], 'full_description' => ['nullable', 'string'], 'cta_label' => ['nullable', 'string', 'max:120'], 'enrollment_available' => ['nullable', 'boolean'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['enrollment_available'] = $request->boolean('enrollment_available');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['full_description'] = RichText::sanitize($data['full_description'] ?? null);
        $course->fill($data)->save();
        return to_route('admin.courses')->with('success', 'Course saved.');
    }

    private function saveProduct(Request $request, Product $product)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('name'))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('products', 'slug')->ignore($product->id)], 'thumbnail' => ['nullable', 'string', 'max:255'], 'product_type' => ['nullable', 'string', 'max:100'], 'availability' => ['required', 'in:draft,available,coming_soon,waitlist,unavailable,archived'], 'short_description' => ['required', 'string'], 'detailed_description' => ['nullable', 'string'], 'version' => ['nullable', 'string', 'max:40'], 'price' => ['nullable', 'numeric', 'min:0'], 'sale_price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'download_format' => ['nullable', 'string', 'max:100'], 'system_requirements' => ['nullable', 'string'], 'demo_url' => ['nullable', 'url'], 'purchase_url' => ['nullable', 'url'], 'documentation_url' => ['nullable', 'url'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['detailed_description'] = RichText::sanitize($data['detailed_description'] ?? null);
        $product->fill($data)->save();
        return to_route('admin.products')->with('success', 'Product saved.');
    }

    private function saveArticle(Request $request, Article $article)
    {
        $request->merge(['slug' => $request->input('slug') ?: Str::slug($request->input('title'))]);
        $data = $request->validate(['article_category_id' => ['nullable', 'exists:article_categories,id'], 'featured_image' => ['nullable', 'string', 'max:255'], 'title' => ['required', 'string', 'max:190'], 'slug' => ['required', 'alpha_dash', 'max:190', Rule::unique('articles', 'slug')->ignore($article->id)], 'excerpt' => ['nullable', 'string'], 'content' => ['required', 'string'], 'author' => ['nullable', 'string', 'max:120'], 'reading_time' => ['nullable', 'integer', 'min:1', 'max:240'], 'status' => ['required', 'in:draft,published,scheduled,archived'], 'published_at' => ['nullable', 'date'], 'is_featured' => ['nullable', 'boolean'], 'seo_title' => ['nullable', 'string', 'max:190'], 'seo_description' => ['nullable', 'string', 'max:300'], 'og_image' => ['nullable', 'string', 'max:255'], 'tags' => ['nullable', 'string', 'max:500']]);
        $data['slug'] = Str::slug($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        if (empty($data['reading_time'])) $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($data['content'])) / 200));
        $data['content'] = RichText::sanitize($data['content']);
        $tagNames = collect(explode(',', (string) ($data['tags'] ?? '')))->map(fn ($tag) => trim($tag))->filter()->unique()->values();
        unset($data['tags']);
        $article->fill($data)->save();
        $tagIds = $tagNames->map(fn ($name) => \App\Models\Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);
        $article->tags()->sync($tagIds);
        return to_route('admin.articles')->with('success', 'Article saved.');
    }
}
