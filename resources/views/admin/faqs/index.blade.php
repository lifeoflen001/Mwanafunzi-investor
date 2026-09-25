@extends('layouts.admin')
@section('title', 'FAQs')
@section('portal-heading', 'FAQs')
@section('content')
<div class="admin-heading"><div><p class="eyebrow">Content / FAQs</p><h1>Frequently asked questions</h1><p>Manage reusable answers across courses, products, pages and learning topics from one controlled desk.</p></div></div>

<section class="admin-card admin-subresource">
    <div class="admin-heading"><div><p class="eyebrow">Add content</p><h2>New FAQ</h2></div></div>
    <form class="admin-form admin-inline-form" method="post" action="{{ route('admin.faqs.store') }}">
        @csrf
        <div class="form-row">
            <label>Attach to
                <select name="target_type" required>
                    <option value="course">Course</option>
                    <option value="product">Product</option>
                    <option value="page">Page / policy</option>
                    <option value="topic">Learning topic</option>
                </select>
            </label>
            <label>Record
                <select name="target_id" required>
                    <optgroup label="Courses">@foreach($courses as $item)<option data-faq-target="course" value="{{ $item->id }}">{{ $item->title }}</option>@endforeach</optgroup>
                    <optgroup label="Products">@foreach($products as $item)<option data-faq-target="product" value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</optgroup>
                    <optgroup label="Pages / policies">@foreach($pages as $item)<option data-faq-target="page" value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</optgroup>
                    <optgroup label="Learning topics">@foreach($topics as $item)<option data-faq-target="topic" value="{{ $item->id }}">{{ $item->title }}</option>@endforeach</optgroup>
                </select>
            </label>
        </div>
        <div class="form-row"><label>Question<input name="question" required maxlength="255"></label><label>Sort order<input type="number" name="sort_order" value="0" min="0" required></label></div>
        <label>Answer<textarea name="answer" rows="3" required maxlength="10000"></textarea></label>
        <button class="button button-dark" type="submit">Add FAQ <span aria-hidden="true">+</span></button>
    </form>
</section>

<div class="admin-table"><div class="admin-table-head"><span>Target</span><span>Question</span><span>Answer</span><span>Order</span><span>Updated</span><span></span></div>
    @forelse($faqs as $faq)
        <div class="admin-table-row">
            <span><strong>{{ ucfirst($faq['source']) }}</strong><small>{{ $faq['target'] }}</small></span>
            <span><strong>{{ $faq['question'] }}</strong></span>
            <span><small>{{ $faq['answer'] }}</small></span>
            <span>{{ $faq['sort_order'] }}</span>
            <span>{{ $faq['updated_at']?->format('M j, Y') }}</span>
            <span class="admin-actions">
                <details><summary>Edit</summary><form class="admin-form admin-inline-form" method="post" action="{{ route('admin.faqs.update', [$faq['source'], $faq['id']]) }}">@csrf @method('put')<label>Question<input name="question" value="{{ $faq['question'] }}" required></label><label>Answer<textarea name="answer" rows="3" required>{{ $faq['answer'] }}</textarea></label><label>Sort order<input type="number" name="sort_order" min="0" value="{{ $faq['sort_order'] }}" required></label><button class="button button-small button-light" type="submit">Save</button></form></details>
                <form method="post" action="{{ route('admin.faqs.manage.destroy', [$faq['source'], $faq['id']]) }}" data-confirm="Remove this FAQ?">@csrf @method('delete')<button type="submit">Remove</button></form>
            </span>
        </div>
    @empty
        <div class="empty-state compact"><p>No FAQs have been created yet.</p></div>
    @endforelse
</div>
{{ $faqs->withQueryString()->links() }}
@endsection
