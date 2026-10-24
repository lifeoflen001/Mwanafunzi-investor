<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReaction;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\CommentReaction;
use App\Notifications\ClientActivityNotification;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class EditorialInteractionController extends Controller
{
    public function react(Request $request, Article $article)
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $type = $request->validate(['type' => ['nullable', Rule::in(['like'])]])['type'] ?? 'like';
        $key = 'article-reaction:'.$request->user()->id.':'.$article->id;
        abort_if(RateLimiter::tooManyAttempts($key, 30), 429, 'Too many reactions. Please try again shortly.');
        RateLimiter::hit($key, 60);

        $reaction = ArticleReaction::where('article_id', $article->id)->where('user_id', $request->user()->id)->where('type', $type)->first();
        $active = false;
        if ($reaction) {
            $reaction->delete();
        } else {
            ArticleReaction::create(['article_id' => $article->id, 'user_id' => $request->user()->id, 'type' => $type]);
            $active = true;
        }

        $count = ArticleReaction::where('article_id', $article->id)->where('type', $type)->count();
        if ($request->expectsJson()) {
            return response()->json(['active' => $active, 'count' => $count]);
        }

        return back()->with('success', $active ? 'Article saved to your activity.' : 'Article removed from your activity.');
    }

    public function comment(Request $request, Article $article)
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $key = 'article-comment:'.($request->user()->id ?? $request->ip()).':'.$article->id;
        abort_if(RateLimiter::tooManyAttempts($key, 5), 429, 'Please wait before posting another comment.');

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:3000'],
            'parent_id' => ['nullable', 'integer', Rule::exists('comments', 'id')->where(fn ($query) => $query->where('article_id', $article->id)->where('status', 'approved')->whereNull('deleted_at'))],
            'website' => ['prohibited'],
            'recaptcha_token' => Recaptcha::rules('comments'),
        ]);
        RateLimiter::hit($key, 300);

        Comment::create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => trim($data['body']),
            'status' => 'pending',
        ]);
        $request->user()->notify(new ClientActivityNotification(
            'Comment received',
            'Your comment is awaiting moderation.',
            route('blog.show', $article).'#comments',
        ));
        $request->session()->forget(\App\Services\AuthenticationRedirectService::COMMENT_DRAFT_KEY);

        return back()->with('success', 'Your comment is awaiting moderation.');
    }

    public function commentReaction(Request $request, Comment $comment)
    {
        $comment->loadMissing('article');
        abort_unless($comment->status === 'approved' && $comment->article && $comment->article->status === 'published' && $comment->article->published_at?->isPast(), 404);

        $data = $request->validate(['type' => ['required', Rule::in(['up', 'down'])]]);
        $key = 'comment-reaction:'.$request->user()->id.':'.$comment->id;
        abort_if(RateLimiter::tooManyAttempts($key, 30), 429, 'Too many reactions. Please try again shortly.');
        RateLimiter::hit($key, 60);

        $reaction = CommentReaction::where('comment_id', $comment->id)->where('user_id', $request->user()->id)->first();
        $active = false;
        if ($reaction?->type === $data['type']) {
            $reaction->delete();
        } elseif ($reaction) {
            $reaction->update(['type' => $data['type']]);
            $active = true;
        } else {
            CommentReaction::create(['comment_id' => $comment->id, 'user_id' => $request->user()->id, 'type' => $data['type']]);
            $active = true;
        }

        $counts = CommentReaction::where('comment_id', $comment->id)->selectRaw("type, count(*) as aggregate")->groupBy('type')->pluck('aggregate', 'type');
        $userType = CommentReaction::where('comment_id', $comment->id)->where('user_id', $request->user()->id)->value('type');
        $payload = ['active' => $active, 'type' => $data['type'], 'user_type' => $userType, 'up_count' => (int) ($counts['up'] ?? 0), 'down_count' => (int) ($counts['down'] ?? 0)];

        if ($request->expectsJson()) return response()->json($payload);

        return back()->with('success', $active ? 'Your reaction was recorded.' : 'Your reaction was removed.');
    }

    public function report(Request $request, Comment $comment)
    {
        $data = $request->validate(['reason' => ['required', Rule::in(['spam', 'abusive', 'off-topic', 'other'])], 'details' => ['nullable', 'string', 'max:1000']]);
        CommentReport::firstOrCreate(['comment_id' => $comment->id, 'user_id' => $request->user()->id], $data);

        return back()->with('success', 'Thank you. The comment has been reported for review.');
    }
}
