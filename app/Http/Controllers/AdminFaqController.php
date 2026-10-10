<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseFaq;
use App\Models\Faq;
use App\Models\LearningTopic;
use App\Models\Page;
use App\Models\Product;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFaqController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $like = "%{$search}%";
        $matches = fn ($query) => $search === ''
            ? $query
            : $query->where(fn ($nested) => $nested->where('question', 'like', $like)->orWhere('answer', 'like', $like));

        // Keep the combined FAQ view paginated in SQL. The previous approach
        // loaded every FAQ from every target table before slicing in PHP.
        $rows = $matches(CourseFaq::query()->join('courses', 'courses.id', '=', 'course_faqs.course_id'))
            ->select('course_faqs.id', DB::raw("'course' as source"), 'courses.title as target', 'course_faqs.question', 'course_faqs.answer', 'course_faqs.sort_order', 'course_faqs.updated_at')
            ->unionAll($matches(Faq::query()->join('products', function ($join) {
                $join->on('products.id', '=', 'faqs.faqable_id')->where('faqs.faqable_type', Product::class);
            }))->select('faqs.id', DB::raw("'product' as source"), 'products.name as target', 'faqs.question', 'faqs.answer', 'faqs.sort_order', 'faqs.updated_at'))
            ->unionAll($matches(Faq::query()->join('pages', function ($join) {
                $join->on('pages.id', '=', 'faqs.faqable_id')->where('faqs.faqable_type', Page::class);
            }))->select('faqs.id', DB::raw("'page' as source"), 'pages.name as target', 'faqs.question', 'faqs.answer', 'faqs.sort_order', 'faqs.updated_at'))
            ->unionAll($matches(Faq::query()->join('learning_topics', function ($join) {
                $join->on('learning_topics.id', '=', 'faqs.faqable_id')->where('faqs.faqable_type', LearningTopic::class);
            }))->select('faqs.id', DB::raw("'topic' as source"), 'learning_topics.title as target', 'faqs.question', 'faqs.answer', 'faqs.sort_order', 'faqs.updated_at'));

        $paginator = DB::query()->fromSub($rows, 'faq_rows')
            ->orderByDesc('updated_at')
            ->paginate(30)
            ->through(fn ($row) => (array) $row);

        return view('admin.faqs.index', [
            'faqs' => $paginator,
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'pages' => Page::orderBy('name')->get(['id', 'name']),
            'topics' => LearningTopic::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $target = $this->target($data['target_type'], (int) $data['target_id']);
        $faq = $target->faqs()->create(collect($data)->only(['question', 'answer', 'sort_order'])->all());
        AdminAudit::record('faq.created', 'Created FAQ for '.$this->targetLabel($target), $faq);
        return back()->with('success', 'FAQ added.');
    }

    public function update(Request $request, string $source, int $id)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string', 'max:10000'], 'sort_order' => ['required', 'integer', 'min:0']]);
        $faq = $source === 'course' ? CourseFaq::findOrFail($id) : Faq::findOrFail($id);
        $faq->update($data);
        AdminAudit::record('faq.updated', 'Updated FAQ', $faq);
        return back()->with('success', 'FAQ updated.');
    }

    public function destroy(string $source, int $id)
    {
        $faq = $source === 'course' ? CourseFaq::findOrFail($id) : Faq::findOrFail($id);
        $faq->delete();
        AdminAudit::record('faq.deleted', 'Deleted FAQ', $faq);
        return back()->with('success', 'FAQ removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'target_type' => ['required', 'in:course,product,page,topic'],
            'target_id' => ['required', 'integer', 'min:1'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:10000'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function target(string $type, int $id): Course|Product|Page|LearningTopic
    {
        return match ($type) {
            'course' => Course::findOrFail($id),
            'product' => Product::findOrFail($id),
            'page' => Page::findOrFail($id),
            'topic' => LearningTopic::findOrFail($id),
        };
    }

    private function targetLabel(Course|Product|Page|LearningTopic $target): string
    {
        return $target->title ?? $target->name;
    }

    private function row(CourseFaq|Faq $faq, string $source, string $target): array
    {
        return [
            'id' => $faq->id,
            'source' => $source,
            'target' => $target,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'sort_order' => $faq->sort_order,
            'updated_at' => $faq->updated_at,
        ];
    }
}
