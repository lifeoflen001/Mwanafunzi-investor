@forelse($comments as $comment)
    <div class="comment-thread {{ $comment->status === 'pending' ? 'comment-thread-pending' : '' }}">
        <article class="comment-item">
            @if($comment->user?->avatar_url)
                <span class="comment-avatar" aria-hidden="true"><img src="{{ $comment->user->avatar_url }}" alt=""></span>
            @else
                <span class="comment-avatar" aria-hidden="true">{{ $comment->initials() }}</span>
            @endif
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

                @if($comment->status === 'approved' && empty($preview))
                    @php($commentUserReaction = auth()->check() ? $comment->reactions->first()?->type : null)
                    <div class="comment-actions">
                        <div class="comment-reactions" data-comment-reaction-group>
                            @auth
                                <form method="post" action="{{ route('comments.reaction', $comment) }}" data-comment-reaction-form>
                                    @csrf
                                    <input type="hidden" name="type" value="up">
                                    <button class="comment-reaction-button {{ $commentUserReaction === 'up' ? 'is-active' : '' }}" type="submit" aria-label="{{ $commentUserReaction === 'up' ? 'Remove thumbs up' : 'Thumbs up this comment' }}" aria-pressed="{{ $commentUserReaction === 'up' ? 'true' : 'false' }}" title="{{ $commentUserReaction === 'up' ? 'Remove thumbs up' : 'Thumbs up' }}">
                                        <span class="comment-reaction-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M7 10v10H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h3Zm0 0h9.5a2.5 2.5 0 0 0 2.45-3l-.75-3A2.5 2.5 0 0 0 15.76 2H15l-4 8v10h6.5a2.5 2.5 0 0 0 2.45-2l.8-4A2.5 2.5 0 0 0 18.3 11H14" /></svg></span>
                                        <span data-comment-reaction-count="up">{{ $comment->up_reactions_count }}</span>
                                        <span class="sr-only">Thumbs up</span>
                                    </button>
                                </form>
                                <form method="post" action="{{ route('comments.reaction', $comment) }}" data-comment-reaction-form>
                                    @csrf
                                    <input type="hidden" name="type" value="down">
                                    <button class="comment-reaction-button {{ $commentUserReaction === 'down' ? 'is-active' : '' }}" type="submit" aria-label="{{ $commentUserReaction === 'down' ? 'Remove thumbs down' : 'Thumbs down this comment' }}" aria-pressed="{{ $commentUserReaction === 'down' ? 'true' : 'false' }}" title="{{ $commentUserReaction === 'down' ? 'Remove thumbs down' : 'Thumbs down' }}">
                                        <span class="comment-reaction-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><g transform="rotate(180 12 12)"><path d="M7 10v10H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h3Zm0 0h9.5a2.5 2.5 0 0 0 2.45-3l-.75-3A2.5 2.5 0 0 0 15.76 2H15l-4 8v10h6.5a2.5 2.5 0 0 0 2.45-2l.8-4A2.5 2.5 0 0 0 18.3 11H14" /></g></svg></span>
                                        <span data-comment-reaction-count="down">{{ $comment->down_reactions_count }}</span>
                                        <span class="sr-only">Thumbs down</span>
                                    </button>
                                </form>
                            @else
                                <a class="comment-reaction-button" href="{{ route('login', ['redirect' => request()->getRequestUri().'#comments']) }}" aria-label="Sign in to give this comment a thumbs up" title="Sign in to react">
                                    <span class="comment-reaction-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M7 10v10H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h3Zm0 0h9.5a2.5 2.5 0 0 0 2.45-3l-.75-3A2.5 2.5 0 0 0 15.76 2H15l-4 8v10h6.5a2.5 2.5 0 0 0 2.45-2l.8-4A2.5 2.5 0 0 0 18.3 11H14" /></svg></span>
                                    <span>{{ $comment->up_reactions_count }}</span>
                                    <span class="sr-only">Sign in to react</span>
                                </a>
                                <a class="comment-reaction-button" href="{{ route('login', ['redirect' => request()->getRequestUri().'#comments']) }}" aria-label="Sign in to give this comment a thumbs down" title="Sign in to react">
                                    <span class="comment-reaction-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><g transform="rotate(180 12 12)"><path d="M7 10v10H4a2 2 0 0 1-2-2v-6a2.5 2.5 0 0 1 2-2h3Zm0 0h9.5a2.5 2.5 0 0 0 2.45-3l-.75-3A2.5 2.5 0 0 0 15.76 2H15l-4 8v10h6.5a2.5 2.5 0 0 0 2.45-2l.8-4A2.5 2.5 0 0 0 18.3 11H14" /></g></svg></span>
                                    <span>{{ $comment->down_reactions_count }}</span>
                                    <span class="sr-only">Sign in to react</span>
                                </a>
                            @endauth
                        </div>

                        @if(auth()->check())
                            <details class="comment-reply">
                                <summary class="comment-reply-trigger" aria-label="Reply to this comment" title="Reply">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6 4 11l5 5M4 11h10a6 6 0 0 1 6 6v1" /></svg>
                                    <span class="sr-only">Reply</span>
                                </summary>
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
