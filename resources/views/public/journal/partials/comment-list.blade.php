@forelse($comments as $comment)
    <div class="comment-thread {{ $comment->status === 'pending' ? 'comment-thread-pending' : '' }}">
        <article class="comment-item">
            <span class="comment-avatar" aria-hidden="true">{{ $comment->initials() }}</span>
            <div class="comment-copy">
                <div class="comment-meta">
                    <strong>{{ $comment->displayName() }}</strong>
                    <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('M j, Y') }}</time>
                    @if($comment->status === 'pending')
                        <span>Awaiting moderation</span>
                    @elseif($comment->edited_at)
                        <span>Edited</span>
                    @endif
                </div>
                <p>{{ $comment->body }}</p>
                @if($comment->status === 'approved' && empty($preview) && auth()->check())
                    <details class="comment-reply">
                        <summary>Reply</summary>
                        <form method="post" action="{{ route('blog.comments.store', $article) }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <textarea name="body" rows="3" required maxlength="3000" placeholder="Write a reply."></textarea>
                            <x-recaptcha action="comments" />
                            <button class="button button-secondary button-small" type="submit">Post reply</button>
                        </form>
                    </details>
                @endif
            </div>
        </article>
        @if($comment->replies->isNotEmpty())
            <div class="comment-replies">
                @include('public.journal.partials.comment-list', ['comments' => $comment->replies])
            </div>
        @endif
    </div>
@empty
    <div class="comment-empty">No comments yet. Start a useful conversation.</div>
@endforelse
