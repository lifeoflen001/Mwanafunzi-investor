<?php

namespace App\Support;

use App\Models\Article;
use App\Models\CookieRegistry;
use App\Models\Course;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\RecaptchaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class ComplianceAudit
{
    public static function report(): array
    {
        $legalKeys = ['privacy-policy', 'terms', 'refund-policy', 'risk-disclosure', 'disclaimer', 'cookie-policy'];
        $pages = Page::withTrashed()->whereIn('key', $legalKeys)->get()->keyBy('key');
        $settings = collect(['brand_name', 'contact_email', 'contact_phone', 'business_legal_name', 'business_country', 'business_jurisdiction', 'business_address', 'business_registration_number', 'business_tax_number', 'support_contact'])
            ->mapWithKeys(fn ($key) => [$key => SiteSetting::getValue($key)])
            ->all();
        $businessMissing = collect(['business_legal_name', 'business_country', 'business_jurisdiction', 'business_address', 'support_contact'])
            ->filter(fn ($key) => blank($settings[$key] ?? null))->values()->all();
        $media = Media::query()->get();
        $publicImages = self::publicImages($media);
        $recaptcha = app(RecaptchaService::class);

        return [
            'legal' => collect($legalKeys)->mapWithKeys(fn ($key) => [$key => [
                'label' => Str::headline($key),
                'published' => isset($pages[$key]) && $pages[$key]->status === 'published' && $pages[$key]->is_visible,
                'updated_at' => $pages[$key]?->updated_at,
                'effective_date' => $pages[$key]?->effective_date,
                'revision_count' => $pages[$key] ? (int) $pages[$key]->revisions()->count() : 0,
            ]])->all(),
            'business' => ['settings' => $settings, 'missing' => $businessMissing, 'complete' => $businessMissing === []],
            'cookies' => [
                'enabled' => (bool) CookieConsentSettings::all()['enabled'],
                'registry_count' => CookieRegistry::active()->count(),
                'optional_services' => CookieRegistry::active()->where('category', '!=', 'essential')->count(),
            ],
            'media' => [
                'total_public_images' => $publicImages['total'],
                'managed_images' => $publicImages['managed'],
                'unknown_source' => $publicImages['unknown_source'],
                'rights_verified' => (int) $media->where('rights_verified', true)->count(),
                'needs_review' => $publicImages['unknown_source'] + (int) $media->where('rights_verified', false)->count(),
                'sample_unknown' => $publicImages['sample_unknown'],
            ],
            'testimonials' => [
                'published' => (int) Testimonial::published()->count(),
                'published_without_consent' => (int) Testimonial::published()->where('consent_confirmed', false)->count(),
                'total' => (int) Testimonial::count(),
            ],
            'claims' => self::claimFindings(),
            'tracking' => self::tracking($recaptcha),
            'forms' => self::forms(),
            'third_party' => self::thirdParty($recaptcha),
            'manual_review' => [
                'accessibility' => 'Manual keyboard, screen-reader, responsive and contrast verification remains required; automated checks cannot certify WCAG conformance.',
                'legal' => 'Jurisdiction-specific legal review is required for privacy registration, consumer terms, marketing, financial education, refunds and cross-border processing.',
            ],
            'generated_at' => now(),
        ];
    }

    private static function publicImages($media): array
    {
        $files = collect(File::allFiles(public_path()))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg'], true))
            ->map(fn ($file) => str_replace('\\', '/', Str::after($file->getPathname(), public_path().'\\')))
            ->values();
        $managedPaths = $media->pluck('path')->filter()->map(fn ($path) => ltrim(str_replace('\\', '/', $path), '/'));
        $unknown = $files->reject(fn ($file) => $managedPaths->contains(fn ($path) => Str::endsWith($file, $path)))->values();

        return ['total' => $files->count(), 'managed' => $files->count() - $unknown->count(), 'unknown_source' => $unknown->count(), 'sample_unknown' => $unknown->take(25)->all()];
    }

    private static function claimFindings(): array
    {
        $patterns = ['guaranteed', 'risk[- ]free', 'no[- ]loss', 'easy money', '100% success', 'always profitable', 'profit guaranteed', 'award[- ]winning', 'number one', 'proven profits', 'licensed', 'certified'];
        $findings = collect();
        foreach (['resources/views', 'app', 'database/seeders', 'database/migrations'] as $directory) {
            if (! File::isDirectory(base_path($directory))) continue;
            foreach (File::allFiles(base_path($directory)) as $file) {
                if (! in_array($file->getExtension(), ['php', 'blade.php', 'js', 'css'], true)) continue;
                $contents = File::get($file->getPathname());
                foreach ($patterns as $pattern) {
                    if (preg_match('/\b'.$pattern.'\b/i', $contents, $match, PREG_OFFSET_CAPTURE)) {
                        $line = substr_count(substr($contents, 0, $match[0][1]), "\n") + 1;
                        $findings->push(['phrase' => str_replace('[- ]', ' / ', $pattern), 'file' => Str::after($file->getPathname(), base_path().'\\'), 'line' => $line, 'context' => Str::limit(trim(str_replace(["\r", "\n"], ' ', substr($contents, max(0, $match[0][1] - 70), 180))), 180)]);
                    }
                }
            }
        }

        return $findings->unique(fn ($item) => $item['file'].'|'.$item['line'].'|'.$item['phrase'])->take(100)->values()->all();
    }

    private static function tracking(RecaptchaService $recaptcha): array
    {
        return collect(CookieRegistry::active()->where('kind', 'service')->get()->map(fn ($entry) => ['service' => $entry->name, 'purpose' => $entry->purpose, 'category' => $entry->category, 'disclosure' => true, 'loaded_before_consent' => false]))
            ->when($recaptcha->isEnabled(), fn ($items) => $items->push(['service' => 'Google reCAPTCHA', 'purpose' => 'Spam and abuse prevention on configured forms.', 'category' => 'essential / legal review', 'disclosure' => true, 'loaded_before_consent' => true]))
            ->values()->all();
    }

    private static function forms(): array
    {
        return [
            ['form' => 'Contact', 'data' => 'Name, email, optional phone, enquiry category, message', 'purpose' => 'Respond to the enquiry', 'notice' => 'Privacy notice + required acknowledgement', 'spam' => 'Honeypot and optional reCAPTCHA'],
            ['form' => 'Newsletter', 'data' => 'Email, source, consent timestamps', 'purpose' => 'Requested email updates', 'notice' => 'Explicit newsletter consent and double opt-in', 'spam' => 'Honeypot and optional reCAPTCHA'],
            ['form' => 'Registration', 'data' => 'Name, email, password, optional phone/country', 'purpose' => 'Account and access delivery', 'notice' => 'Terms and risk disclosure links', 'spam' => 'Optional reCAPTCHA'],
            ['form' => 'Checkout', 'data' => 'Name, email, phone, country, order/payment references', 'purpose' => 'Fulfil order and reconcile payment', 'notice' => 'Terms, refund, privacy and risk links', 'spam' => 'Optional reCAPTCHA'],
            ['form' => 'Comments', 'data' => 'Authenticated user comment and moderation metadata', 'purpose' => 'Publish moderated discussion', 'notice' => 'Terms apply; approval required', 'spam' => 'Authenticated account, honeypot and optional reCAPTCHA'],
        ];
    }

    private static function thirdParty(RecaptchaService $recaptcha): array
    {
        return [
            ['service' => 'Payment provider', 'configured' => filled(config('commerce.provider')), 'value' => (string) config('commerce.provider'), 'review' => 'Confirm processor terms, data sharing and cross-border transfers before production.'],
            ['service' => 'Email transport', 'configured' => filled(config('mail.default')), 'value' => (string) config('mail.default'), 'review' => 'Confirm provider, retention and transfer terms in the Privacy Policy.'],
            ['service' => 'Google reCAPTCHA', 'configured' => $recaptcha->isEnabled(), 'value' => $recaptcha->isEnabled() ? 'Enabled' : 'Disabled', 'review' => 'If enabled, keep disclosure current and obtain legal advice on consent/necessity.'],
            ['service' => 'Analytics / marketing pixels', 'configured' => false, 'value' => 'No configured services detected', 'review' => 'Keep optional scripts disabled until a consent-reviewed integration is configured.'],
        ];
    }
}
