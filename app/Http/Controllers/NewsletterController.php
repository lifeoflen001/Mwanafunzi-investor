<?php

namespace App\Http\Controllers;

use App\Rules\Recaptcha;
use App\Services\NewsletterSubscriptionService;
use App\Support\NewsletterSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request, NewsletterSubscriptionService $subscriptions)
    {
        abort_unless((bool) NewsletterSettings::all()['enabled'], 404);

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'website' => ['nullable', 'string', 'max:0'],
            'recaptcha_token' => Recaptcha::rules('newsletter'),
        ]);

        if (filled($request->input('website'))) return back()->with('newsletter_status', 'check-inbox');

        $result = $subscriptions->subscribe(
            Str::lower(trim($data['email'])),
            'footer',
            $request->user()?->id,
            $request,
        );

        return back()->with('newsletter_status', match ($result['outcome']) {
            'already' => 'already',
            'pending' => 'check-inbox',
            default => 'subscribed',
        });
    }

    public function confirm(Request $request, string $token, NewsletterSubscriptionService $subscriptions)
    {
        $subscriber = $subscriptions->confirm($token);
        return view('public.newsletter.result', ['type' => $subscriber ? 'confirmed' : 'invalid']);
    }

    public function unsubscribe(string $token, NewsletterSubscriptionService $subscriptions)
    {
        $subscriber = $subscriptions->unsubscribe($token);
        return view('public.newsletter.result', ['type' => $subscriber ? 'unsubscribed' : 'invalid']);
    }
}
