<div class="{{ $lesson->trashed() ? 'is-archived' : '' }}">
    @if($lesson->trashed())
        <span>{{ $lesson->title }} <em class="status-badge">Archived</em></span>
        <form method="post" action="{{ route('admin.lessons.restore', $lesson->id) }}">@csrf<button type="submit">Restore</button></form>
    @else
        <details class="lesson-editor">
            <summary>{{ $lesson->title }} <span class="status-badge">{{ $lesson->is_published ? 'Published' : 'Draft' }}</span></summary>
            <form class="admin-form admin-inline-form" method="post" action="{{ route('admin.lessons.update', $lesson) }}">
                @csrf @method('put')
                <label>Lesson title<input name="title" value="{{ $lesson->title }}" required></label>
                <div class="form-row"><label>Sort order<input type="number" name="sort_order" min="0" value="{{ $lesson->sort_order }}" required></label><label class="consent"><input type="checkbox" name="is_published" value="1" @checked($lesson->is_published)> <span>Published</span></label></div>
                @include('admin.components.rich-text-field', ['name' => 'content', 'label' => 'Lesson content', 'value' => $lesson->content, 'idSuffix' => 'lesson-'.$lesson->id])
                <button class="button button-small button-light" type="submit">Save lesson <span aria-hidden="true">↗</span></button>
            </form>
        </details>
        <form method="post" action="{{ route('admin.lessons.destroy', $lesson) }}">@csrf @method('delete')<button type="submit">Remove</button></form>
    @endif
</div>
