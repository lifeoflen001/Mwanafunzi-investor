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
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class AdminFaqController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $rows = collect();

        CourseFaq::with('course')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%")))
            ->get()
            ->each(fn (CourseFaq $faq) => $rows->push($this->row($faq, 'course', $faq->course?->title ?: 'Course')));

        Faq::with('faqable')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%")))
            ->get()
            ->each(function (Faq $faq) use ($rows) {
                $target = $faq->faqable;
                if (! $target) return;
                $type = match (true) {
                    $target instanceof Product => 'product',
                    $target instanceof Page => 'page',
                    $target instanceof LearningTopic => 'topic',
                    default => null,
                };
                if ($type) $rows->push($this->row($faq, $type, $target->name ?? $target->title));
            });

        $rows = $rows->sortByDesc('updated_at')->values();
        $perPage = 30;
        $page = Paginator::resolveCurrentPage();
        $paginator = new Paginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

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
