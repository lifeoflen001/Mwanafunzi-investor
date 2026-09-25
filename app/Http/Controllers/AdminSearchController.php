<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $term = trim((string) ($data['q'] ?? ''));
        $results = collect();

        if ($term !== '') {
            $like = '%'.$term.'%';
            $results = $results
                ->merge(Page::withTrashed()->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('key', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Page', $item->name, route('admin.pages.edit', $item), $item->status)))
                ->merge(Course::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Course', $item->title, route('admin.courses.edit', $item), $item->status)))
                ->merge(Product::withTrashed()->where(fn ($query) => $query->where('name', 'like', $like)->orWhere('slug', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Product', $item->name, route('admin.products.edit', $item), $item->availability)))
                ->merge(Article::withTrashed()->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('slug', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Article', $item->title, route('admin.articles.edit', $item), $item->status)))
                ->merge(Order::where(fn ($query) => $query->where('order_number', 'like', $like)->orWhere('customer_email', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Order', $item->order_number, route('admin.commerce.orders.show', $item), $item->payment_status)))
                ->merge(ContactMessage::where(fn ($query) => $query->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('message', 'like', $like))->limit(8)->get()->map(fn ($item) => $this->result('Enquiry', $item->name, route('admin.messages'), $item->status)));
        }

        return view('admin.search.index', compact('term', 'results'));
    }

    private function result(string $type, string $title, string $url, ?string $meta): array
    {
        return compact('type', 'title', 'url', 'meta');
    }
}
