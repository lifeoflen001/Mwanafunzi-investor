<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Media;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'type' => ['nullable', 'in:all,Page,Course,Product,Article,Order,Enquiry,Customer,Topic,Media,Service,Project,Testimonial']]);
        $term = trim((string) ($data['q'] ?? ''));
        $type = $data['type'] ?? 'all';
        $results = $this->searchResults($term, 20, $type);

        return view('admin.search.index', compact('term', 'type', 'results'));
    }

    public function suggestions(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $term = trim((string) ($data['q'] ?? ''));
        $results = $term === '' ? collect() : $this->searchResults($term, 4);

        return response()->json([
            'total' => $results->count(),
            'groups' => $results->groupBy('type')->map(fn ($items, $type) => ['type' => $type, 'items' => $items->values()])->values(),
        ]);
    }

    private function searchResults(string $term, int $limit, string $type = 'all')
    {
        if ($term === '') return collect();
        $like = '%'.$term.'%';
        $include = fn (string $name): bool => $type === 'all' || $type === $name;
        $results = collect();

        if ($include('Page')) $results = $results->merge(Page::withTrashed()->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('key', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Page', $item->name, route('admin.pages.edit', $item), $item->status)));
        if ($include('Course')) $results = $results->merge(Course::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Course', $item->title, route('admin.courses.edit', $item), $item->status)));
        if ($include('Product')) $results = $results->merge(Product::withTrashed()->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Product', $item->name, route('admin.products.edit', $item), $item->availability)));
        if ($include('Article')) $results = $results->merge(Article::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Article', $item->title, route('admin.articles.edit', $item), $item->status)));
        if ($include('Order')) $results = $results->merge(Order::where(fn ($query) => $query->where('order_number', 'like', $like)->orWhere('customer_email', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Order', $item->order_number, route('admin.commerce.orders.show', $item), $item->payment_status)));
        if ($include('Enquiry')) $results = $results->merge(ContactMessage::where(fn ($query) => $query->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('message', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Enquiry', $item->name, route('admin.messages.show', $item), $item->status)));
        if ($include('Customer')) $results = $results->merge(User::where('is_admin', false)->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Customer', $item->name, route('admin.customers.show', $item), $item->status)));
        if ($include('Topic')) $results = $results->merge(LearningTopic::where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Topic', $item->title, route('admin.topics.edit', $item), $item->status)));
        if ($include('Media')) $results = $results->merge(Media::where(fn ($query) => $query->where('filename', 'like', $like)->orWhere('title', 'like', $like)->orWhere('alt_text', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Media', $item->title ?: $item->filename, route('admin.media', ['q' => $term]), $item->mime_type)));
        if ($include('Service')) $results = $results->merge(Service::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Service', $item->title, route('admin.services.edit', $item), $item->is_active ? 'active' : 'hidden')));
        if ($include('Project')) $results = $results->merge(Project::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Project', $item->title, route('admin.projects.edit', $item), $item->status)));
        if ($include('Testimonial')) $results = $results->merge(Testimonial::withTrashed()->where(fn ($query) => $query->where('client_name', 'like', $like)->orWhere('company', 'like', $like)->orWhere('testimonial', 'like', $like))->limit($limit)->get()->map(fn ($item) => $this->result('Testimonial', $item->client_name, route('admin.testimonials.edit', $item), $item->is_published ? 'published' : 'draft')));

        return $results->values();
    }

    private function result(string $type, string $title, string $url, mixed $meta): array
    {
        $meta = is_object($meta) && isset($meta->value) ? $meta->value : (string) ($meta ?? '');
        return compact('type', 'title', 'url', 'meta');
    }
}
