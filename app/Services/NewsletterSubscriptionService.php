<?php

namespace App\Services;

use App\Mail\NewsletterConfirmationMail;
use App\Mail\NewsletterConfirmedMail;
use App\Models\NewsletterSubscriber;
use App\Support\AdminAudit;
use App\Support\NewsletterSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterSubscriptionService
{
    public function subscribe(string $email, string $source = 'footer', ?int $userId = null, ?Request $request = null): array
    {
        $email = Str::lower(trim($email));
        $source = in_array($source, NewsletterSubscriber::SOURCES, true) ? $source : 'footer';
        $confirmationToken = Str::random(64);
        $unsubscribeToken = Str::random(64);
        $requiresConfirmation = (bool) NewsletterSettings::all()['confirmation_required'];
        $existing = NewsletterSubscriber::where('email', $email)->first();
        if ($existing?->status === NewsletterSubscriber::ACTIVE) return ['subscriber' => $existing, 'outcome' => 'already'];

        $subscriber = DB::transaction(function () use ($email, $source, $userId, $request, $confirmationToken, $unsubscribeToken, $requiresConfirmation) {
            $subscriber = NewsletterSubscriber::where('email', $email)->first();
            if ($subscriber?->status === NewsletterSubscriber::ACTIVE) return $subscriber;

            $subscriber ??= new NewsletterSubscriber(['email' => $email]);
            $subscriber->fill([
                'user_id' => $subscriber->user_id ?: $userId,
                'email' => $email,
                'source' => $source,
                'status' => $requiresConfirmation ? NewsletterSubscriber::PENDING : NewsletterSubscriber::ACTIVE,
                'confirmed_at' => $requiresConfirmation ? null : now(),
                'unsubscribed_at' => null,
                'confirmation_token_hash' => $requiresConfirmation ? hash('sha256', $confirmationToken) : null,
                'unsubscribe_token_hash' => hash('sha256', $unsubscribeToken),
                'unsubscribe_token' => $unsubscribeToken,
                'consent_version' => NewsletterSettings::all()['consent_version'],
                'consent_ip' => $request?->ip(),
                'consent_user_agent' => Str::limit((string) $request?->userAgent(), 500, ''),
                'last_confirmation_sent_at' => $requiresConfirmation ? now() : null,
            ]);
            $subscriber->save();

            return $subscriber;
        });

        if ($subscriber->status === NewsletterSubscriber::PENDING) {
            $this->queueConfirmation($subscriber, $confirmationToken);
            return ['subscriber' => $subscriber, 'outcome' => 'pending'];
        }

        if ($subscriber->wasRecentlyCreated || $subscriber->wasChanged('status')) {
            $this->queueConfirmed($subscriber);
        }

        return ['subscriber' => $subscriber, 'outcome' => $subscriber->wasRecentlyCreated ? 'active' : 'resubscribed'];
    }

    public function resend(NewsletterSubscriber $subscriber): void
    {
        if ($subscriber->status !== NewsletterSubscriber::PENDING) return;
        $token = Str::random(64);
        $subscriber->forceFill(['confirmation_token_hash' => hash('sha256', $token), 'last_confirmation_sent_at' => now()])->save();
        $this->queueConfirmation($subscriber, $token);
    }

    public function confirm(string $token): ?NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token_hash', hash('sha256', $token))->first();
        if (! $subscriber || $subscriber->status !== NewsletterSubscriber::PENDING) return null;

        $subscriber->forceFill(['status' => NewsletterSubscriber::ACTIVE, 'confirmed_at' => now(), 'confirmation_token_hash' => null])->save();
        AdminAudit::record('newsletter.subscription_confirmed', 'Newsletter subscription confirmed for '.$subscriber->email, $subscriber);
        $this->queueConfirmed($subscriber);
        return $subscriber;
    }

    public function unsubscribe(string $token): ?NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token_hash', hash('sha256', $token))->first();
        if (! $subscriber) return null;

        $subscriber->forceFill(['status' => NewsletterSubscriber::UNSUBSCRIBED, 'unsubscribed_at' => now(), 'confirmation_token_hash' => null])->save();
        AdminAudit::record('newsletter.unsubscribed', 'Newsletter subscription unsubscribed for '.$subscriber->email, $subscriber);
        return $subscriber;
    }

    private function queueConfirmation(NewsletterSubscriber $subscriber, string $token): void
    {
        Mail::to($subscriber->email)->queue(new NewsletterConfirmationMail($subscriber, $token));
    }

    private function queueConfirmed(NewsletterSubscriber $subscriber): void
    {
        Mail::to($subscriber->email)->queue(new NewsletterConfirmedMail($subscriber));
    }
}
