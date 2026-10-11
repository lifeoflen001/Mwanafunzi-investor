<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Services\NewsletterSubscriptionService;
use App\Support\AdminAudit;
use App\Support\NewsletterSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminNewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::with('user')->latest();
        if ($search = trim((string) $request->input('search'))) $query->where('email', 'like', '%'.$search.'%');
        if ($request->filled('status') && in_array($request->input('status'), NewsletterSubscriber::STATUSES, true)) $query->where('status', $request->input('status'));
        if ($request->filled('source') && in_array($request->input('source'), NewsletterSubscriber::SOURCES, true)) $query->where('source', $request->input('source'));
        if ($request->filled('date')) $query->whereDate('created_at', $request->input('date'));

        return view('admin.newsletter.index', [
            'subscribers' => $query->paginate(25)->withQueryString(),
            'stats' => [
                'total' => NewsletterSubscriber::count(),
                'active' => NewsletterSubscriber::active()->count(),
                'pending' => NewsletterSubscriber::pending()->count(),
                'unsubscribed' => NewsletterSubscriber::unsubscribed()->count(),
                'new_this_month' => NewsletterSubscriber::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'statuses' => NewsletterSubscriber::STATUSES,
            'sources' => NewsletterSubscriber::SOURCES,
        ]);
    }

    public function show(NewsletterSubscriber $subscriber)
    {
        return view('admin.newsletter.show', compact('subscriber'));
    }

    public function store(Request $request, NewsletterSubscriptionService $subscriptions)
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:190'], 'source' => ['required', 'in:'.implode(',', NewsletterSubscriber::SOURCES)]]);
        $result = $subscriptions->subscribe(Str::lower(trim($data['email'])), $data['source'], null, $request);
        AdminAudit::record('newsletter.admin_added', 'Admin added newsletter subscriber '.$data['email'], $result['subscriber']);
        return back()->with('success', $result['outcome'] === 'already' ? 'That email is already subscribed.' : 'Subscriber added. Confirmation is pending if required.');
    }

    public function resend(NewsletterSubscriber $subscriber, NewsletterSubscriptionService $subscriptions)
    {
        $subscriptions->resend($subscriber);
        AdminAudit::record('newsletter.confirmation_resent', 'Resent newsletter confirmation to '.$subscriber->email, $subscriber);
        return back()->with('success', 'Confirmation email queued.');
    }

    public function unsubscribe(NewsletterSubscriber $subscriber, NewsletterSubscriptionService $subscriptions)
    {
        $subscriber->forceFill(['status' => NewsletterSubscriber::UNSUBSCRIBED, 'unsubscribed_at' => now(), 'confirmation_token_hash' => null])->save();
        AdminAudit::record('newsletter.admin_unsubscribed', 'Admin unsubscribed '.$subscriber->email, $subscriber);
        return back()->with('success', 'Subscriber unsubscribed.');
    }

    public function restore(NewsletterSubscriber $subscriber, NewsletterSubscriptionService $subscriptions)
    {
        $result = $subscriptions->subscribe($subscriber->email, $subscriber->source, $subscriber->user_id, request());
        AdminAudit::record('newsletter.admin_resubscribed', 'Admin restored newsletter subscription for '.$subscriber->email, $result['subscriber']);
        return back()->with('success', 'Subscriber restored with fresh consent handling.');
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $email = $subscriber->email;
        AdminAudit::record('newsletter.admin_removed', 'Admin removed newsletter subscriber '.$email, $subscriber);
        $subscriber->delete();
        return to_route('admin.newsletter')->with('success', 'Subscriber removed.');
    }

    public function export(Request $request)
    {
        $query = NewsletterSubscriber::with('user')->latest();
        if ($request->filled('status') && in_array($request->input('status'), NewsletterSubscriber::STATUSES, true)) $query->where('status', $request->input('status'));
        if ($request->filled('source') && in_array($request->input('source'), NewsletterSubscriber::SOURCES, true)) $query->where('source', $request->input('source'));

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Status', 'Source', 'Subscribed Date', 'Confirmed Date', 'Unsubscribed Date']);
            foreach ($query->cursor() as $subscriber) {
                fputcsv($handle, array_map([$this, 'safeCsv'], [$subscriber->email, $subscriber->status, $subscriber->source, $subscriber->created_at?->toIso8601String(), $subscriber->confirmed_at?->toIso8601String(), $subscriber->unsubscribed_at?->toIso8601String()]));
            }
            fclose($handle);
        }, 'newsletter-subscribers-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function settings()
    {
        return view('admin.newsletter.settings', ['settings' => NewsletterSettings::all()]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'heading' => ['required', 'string', 'max:190'], 'description' => ['required', 'string', 'max:1000'], 'success_message' => ['required', 'string', 'max:300'],
            'sender_name' => ['required', 'string', 'max:190'], 'sender_email' => ['required', 'email', 'max:190'], 'reply_to_email' => ['required', 'email', 'max:190'],
            'privacy_url' => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)/i'], 'consent_copy' => ['required', 'string', 'max:500'],
            'enabled' => ['nullable', 'boolean'], 'confirmation_required' => ['nullable', 'boolean'], 'footer_visible' => ['nullable', 'boolean'],
        ]);
        NewsletterSettings::save([...$data, 'enabled' => $request->boolean('enabled'), 'confirmation_required' => $request->boolean('confirmation_required'), 'footer_visible' => $request->boolean('footer_visible')]);
        AdminAudit::record('newsletter.settings_updated', 'Updated newsletter settings');
        return back()->with('success', 'Newsletter settings updated.');
    }

    private function safeCsv(?string $value): string
    {
        $value = (string) $value;
        return in_array($value[0] ?? '', ['=', '+', '-', '@'], true) ? "'".$value : $value;
    }
}
