<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCommentController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', 'string', Rule::in([...Comment::STATUSES, 'reported'])]]);
        $comments = Comment::with(['article', 'user', 'parent'])
            ->withCount(['replies', 'reports'])
            ->when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('body', 'like', '%'.$term.'%')->orWhere('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%'))->orWhereHas('article', fn ($article) => $article->where('title', 'like', '%'.$term.'%'))))
            ->when(($data['status'] ?? null) === 'reported', fn ($query) => $query->whereHas('reports', fn ($report) => $report->where('status', 'pending')))
            ->when(($data['status'] ?? null) && ($data['status'] ?? null) !== 'reported', fn ($query) => $query->where('status', $data['status']))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = collect(Comment::STATUSES)->mapWithKeys(fn ($status) => [$status => Comment::where('status', $status)->count()]);

        return view('admin.comments.index', compact('comments', 'counts'));
    }

    public function show(Comment $comment)
    {
        $comment->load(['article', 'user', 'parent', 'replies.user', 'reports.user']);
        return view('admin.comments.show', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        $data = $request->validate(['status' => ['required', Rule::in(Comment::STATUSES)]]);
        $comment->update(['status' => $data['status'], 'approved_at' => $data['status'] === 'approved' ? ($comment->approved_at ?: now()) : $comment->approved_at]);
        AdminAudit::record('comment.'.$data['status'], ucfirst($data['status']).' comment on '.($comment->article?->title ?: 'article'), $comment);

        return back()->with('success', 'Comment moderation status updated.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        AdminAudit::record('comment.deleted', 'Deleted comment', $comment);

        return back()->with('success', 'Comment removed.');
    }
}
