<?php

namespace App\Services;

use App\Models\Article;
use App\Support\SafeRedirect;
use Illuminate\Http\Request;

class AuthenticationRedirectService
{
    public const DESTINATION_KEY = 'auth_continuation';
    public const EXPIRY_KEY = 'auth_continuation_expires_at';
    public const COMMENT_DRAFT_KEY = 'auth_comment_draft';
    private const LEGACY_DESTINATION_KEY = 'auth.redirect';

    public function defaultDestination(): string
    {
        return route('account.dashboard');
    }

    public function rememberFromRequest(Request $request): ?string
    {
        if (! $request->has('redirect')) {
            return $request->session()->has(self::DESTINATION_KEY)
                ? $this->current($request)
                : null;
        }

        return $this->remember($request, $request->query('redirect'));
    }

    public function remember(Request $request, mixed $destination): string
    {
        $destination = $this->normalizeForClient($request, $destination);

        $request->session()->put([
            self::DESTINATION_KEY => $destination,
            self::EXPIRY_KEY => now()->addMinutes(30)->timestamp,
        ]);

        return $destination;
    }

    public function current(Request $request, ?string $default = null): string
    {
        $expiresAt = (int) $request->session()->get(self::EXPIRY_KEY, 0);
        $legacyDestination = $request->session()->get(self::LEGACY_DESTINATION_KEY);
        $destination = $expiresAt > 0 && $expiresAt < now()->timestamp
            ? null
            : ($request->session()->get(self::DESTINATION_KEY) ?: (is_string($legacyDestination) ? $legacyDestination : null));

        if ($expiresAt > 0 && $expiresAt < now()->timestamp) {
            $this->forget($request);
        }

        return $this->normalizeForClient($request, is_string($destination) ? $destination : null, $default ?: $this->defaultDestination());
    }

    public function pull(Request $request, ?string $default = null): string
    {
        $destination = $this->current($request, $default);
        $this->forget($request);

        return $destination;
    }

    public function captureGuestRequest(Request $request): string
    {
        $destination = $this->destinationFor($request);
        $this->remember($request, $destination);

        if ($this->isCommentSubmission($request)) {
            $body = trim((string) $request->input('body'));

            if ($body !== '') {
                $article = $request->route('article');
                $articleId = is_object($article)
                    ? $article->id
                    : Article::where('slug', (string) $article)->value('id');

                $request->session()->put(self::COMMENT_DRAFT_KEY, [
                    'article_id' => $articleId,
                    'body' => mb_substr($body, 0, 3000),
                    'parent_id' => $request->input('parent_id'),
                    'created_at' => now()->timestamp,
                ]);
            }
        }

        return $destination;
    }

    public function destinationFor(Request $request): string
    {
        $article = $request->route('article');

        if ($article && $request->routeIs('blog.comments.store', 'blog.reaction')) {
            return route($request->routeIs('blog.comments.store') ? 'blog.show' : 'blog.show', $article)
                .($request->routeIs('blog.comments.store') ? '#comments' : '');
        }

        if ($article && $request->routeIs('journal.comments.store', 'journal.reaction')) {
            return route('journal.show', $article).($request->routeIs('journal.comments.store') ? '#comments' : '');
        }

        return $this->normalizeForClient($request, $request->getRequestUri(), $this->defaultDestination());
    }

    public function commentDraftFor(Request $request, int $articleId): ?array
    {
        $draft = $request->session()->get(self::COMMENT_DRAFT_KEY);

        if (! is_array($draft) || (int) ($draft['article_id'] ?? 0) !== $articleId) {
            return null;
        }

        if ((int) ($draft['created_at'] ?? 0) < now()->subMinutes(30)->timestamp) {
            $request->session()->forget(self::COMMENT_DRAFT_KEY);

            return null;
        }

        return $draft;
    }

    public function forget(Request $request): void
    {
        $request->session()->forget([self::DESTINATION_KEY, self::EXPIRY_KEY, self::LEGACY_DESTINATION_KEY, 'auth.redirect.expires_at']);
    }

    private function normalizeForClient(Request $request, mixed $destination, ?string $default = null): string
    {
        if (is_string($destination)) {
            $parsed = parse_url($destination);

            if (is_array($parsed) && isset($parsed['host'])) {
                $sameHost = strcasecmp((string) $parsed['host'], (string) $request->getHost()) === 0;
                $samePort = ! isset($parsed['port']) || (int) $parsed['port'] === (int) $request->getPort();

                if (! $sameHost || ! $samePort) {
                    $destination = null;
                } else {
                    $destination = ($parsed['path'] ?? '/').(isset($parsed['query']) ? '?'.$parsed['query'] : '').(isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '');
                }
            }
        }

        $normalized = SafeRedirect::normalize(is_string($destination) ? $destination : null, $default ?: $this->defaultDestination());
        $path = (string) parse_url($normalized, PHP_URL_PATH);

        if ($path === '' || $path === '/login' || $path === '/register' || $path === '/logout' || $path === '/admin' || str_starts_with($path, '/admin/')) {
            return $default ?: $this->defaultDestination();
        }

        return $normalized;
    }

    private function isCommentSubmission(Request $request): bool
    {
        return $request->isMethod('post') && $request->routeIs('blog.comments.store', 'journal.comments.store');
    }
}
