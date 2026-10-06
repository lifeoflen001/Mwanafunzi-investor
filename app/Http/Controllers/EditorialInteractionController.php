<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReaction;
use App\Models\Comment;
use App\Models\CommentReport;
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
            'parent_id' => ['nullable', 'integer', Rule::exists('comments', 'id')->where(fn ($query) => $query->where('article_id', $article->id)->whereNull('parent_id')->where('status', 'approved'))],
            'website' => ['prohibited'],
        ]);
        RateLimiter::hit($key, 300);

        Comment::create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => trim($data['body']),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your comment is awaiting moderation.');
    }

    public function report(Request $request, Comment $comment)
    {
        $data = $request->validate(['reason' => ['required', Rule::in(['spam', 'abusive', 'off-topic', 'other'])], 'details' => ['nullable', 'string', 'max:1000']]);
        CommentReport::firstOrCreate(['comment_id' => $comment->id, 'user_id' => $request->user()->id], $data);

        return back()->with('success', 'Thank you. The comment has been reported for review.');
    }
}
