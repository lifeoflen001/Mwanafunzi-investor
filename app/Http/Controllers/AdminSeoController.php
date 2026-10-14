<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\LearningTopic;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Support\Seo;

class AdminSeoController extends Controller
{
    public function index()
    {
        $pages = collect();
        $add = function (string $label, string $url, ?string $title, ?string $description, ?string $robots = null, ?string $canonical = null, ?string $keyword = null, ?string $editUrl = null, ?string $type = null, ?string $image = null) use (&$pages): void {
            $pages->push(['label' => $label, 'url' => $url, 'title' => $title, 'description' => $description, 'robots' => $robots ?: 'index,follow', 'canonical' => $canonical ?: $url, 'expectedCanonical' => $url, 'keyword' => $keyword, 'editUrl' => $editUrl, 'type' => $type ?: 'Public page', 'image' => $image]);
        };

        foreach ([
            ['home', 'Home', route('home'), 'Mwanafunzi Investor — Become a Student of Money', 'Financial education, digital software and creative systems for clearer decisions, disciplined work and useful progress.', 'Home'],
            ['academy', 'Financial Academy', route('forex-academy'), 'Financial Academy — Mwanafunzi Investor', 'Structured forex education for probability, risk planning, mechanical analysis and disciplined execution.', 'Academy landing'],
            ['learn', 'Learn', route('learn'), 'Learn — Mwanafunzi Investor', 'Practical learning paths for market structure, probability, risk management and disciplined trading.', 'Learning index'],
            ['courses', 'Courses', route('courses'), 'Courses — Mwanafunzi Investor', 'Structured financial education courses for deliberate trading practice.', 'Course catalogue'],
            ['tools', 'Tools', route('tools'), 'Tools — Mwanafunzi Investor', 'Focused digital tools for journaling, calculation and risk-first trading decisions.', 'Tool catalogue'],
            ['blog', 'Blog', route('blog'), 'Blog — Mwanafunzi Investor', 'Market education, trading reviews, platform updates and practical notes from Mwanafunzi Investor.', 'Blog'],
            ['about', 'About', route('about'), 'About — Mwanafunzi Investor', 'Learn about Mwanafunzi Investor, its approach to money, systems, software and creative work.', 'About'],
            ['contact', 'Contact', route('contact'), 'Contact — Mwanafunzi Investor', 'Contact the Mwanafunzi Investor desk for general questions, course enquiries, product support or partnerships.', 'Contact'],
            ['development', 'Digital Software', route('digital-systems'), 'Digital Software — Mwanafunzi Investor', 'Websites, dashboards and operational software shaped around real business needs.', 'Service division'],
            ['studio', 'Creative Studio', route('creative-studio'), 'Creative Studio — Mwanafunzi Investor', 'Thoughtful visual identity, content and creative systems for brands with a point of view.', 'Service division'],
            ['services', 'Services', route('services'), 'Services — Mwanafunzi Investor', 'Digital software, websites and operational tools shaped around real business needs.', 'Services index'],
            ['projects', 'Projects', route('projects'), 'Projects — Mwanafunzi Investor', 'Selected software, web and creative work from Mwanafunzi Investor.', 'Projects index'],
            ['student-of-money', 'Student of Money', route('student-of-money'), 'Student of Money — Mwanafunzi Investor', 'A practical philosophy for learning about money, probability and disciplined decisions.', 'Standalone page'],
            ['privacy-policy', 'Privacy Policy', route('legal', 'privacy-policy'), 'Privacy Policy — Mwanafunzi Investor', 'How Mwanafunzi Investor handles personal information and privacy choices.', 'Policy'],
            ['terms', 'Terms', route('legal', 'terms'), 'Terms — Mwanafunzi Investor', 'Terms that govern use of the Mwanafunzi Investor website, learning content and services.', 'Policy'],
            ['risk-disclosure', 'Risk Disclosure', route('legal', 'risk-disclosure'), 'Risk Disclosure — Mwanafunzi Investor', 'Important risks associated with trading education and market participation.', 'Policy'],
            ['refund-policy', 'Refund Policy', route('legal', 'refund-policy'), 'Refund Policy — Mwanafunzi Investor', 'Refund terms for eligible Mwanafunzi Investor products and courses.', 'Policy'],
            ['disclaimer', 'Disclaimer', route('legal', 'disclaimer'), 'Disclaimer — Mwanafunzi Investor', 'Educational and informational disclaimer for Mwanafunzi Investor content.', 'Policy'],
            ['cookie-policy', 'Cookie Policy', route('cookie-policy'), 'Cookie Policy — Mwanafunzi Investor', 'How Mwanafunzi Investor uses cookies and manages consent.', 'Policy'],
            ['site-map', 'Site map', route('site-map'), 'Site map — Mwanafunzi Investor', 'Browse the public pages, learning resources, services and policies available from Mwanafunzi Investor.', 'Utility page'],
        ] as [$key, $label, $url, $title, $description, $type]) {
            $pageKey = $key === 'academy' ? 'learn' : $key;
            $target = Seo::keywordTargets($pageKey);
            $page = Page::published()->where('key', $pageKey)->first();
            $add($label, $url, $page?->seo_title ?: $title, $page?->seo_description ?: $description, $page?->robots, $page?->canonical_url ?: $url, $page?->primary_keyword ?: ($target['primary'] ?? null), $page ? route('admin.pages.edit', $page) : null, $type, $page?->hero_image);
        }

        Page::published()->whereNotIn('key', ['home', 'learn', 'courses', 'tools', 'journal', 'about', 'contact', 'student-of-money', 'development', 'studio', 'privacy-policy', 'terms', 'risk-disclosure', 'refund-policy', 'disclaimer', 'cookie-policy'])->get()->each(function ($page) use ($add): void {
            $url = route('pages.show', ['slug' => $page->slug]);
            $add($page->name, $url, $page->seo_title ?: $page->name.' — Mwanafunzi Investor', $page->seo_description ?: $page->hero_summary, $page->robots, $page->canonical_url ?: $url, $page->primary_keyword, route('admin.pages.edit', $page), 'Standalone page', $page->hero_image);
        });
        Course::published()->get()->each(fn ($item) => $add($item->title, route('courses.show', $item), $item->seo_title ?: $item->title.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_description, $item->robots, $item->canonical_url ?: route('courses.show', $item), $item->primary_keyword, route('admin.courses.edit', $item), 'Course', $item->featured_image));
        Product::available()->get()->each(fn ($item) => $add($item->name, route('tools.show', $item), $item->seo_title ?: $item->name.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_description, $item->robots, $item->canonical_url ?: route('tools.show', $item), $item->primary_keyword, route('admin.products.edit', $item), 'Tool / resource', $item->thumbnail));
        LearningTopic::where('is_published', true)->where('status', 'published')->get()->each(fn ($item) => $add($item->title, route('learn.show', $item), $item->seo_title ?: $item->title.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_description, $item->robots, $item->canonical_url ?: route('learn.show', $item), $item->primary_keyword, route('admin.topics.edit', $item), 'Learning topic', $item->hero_image ?: $item->image));
        Article::published()->get()->each(fn ($item) => $add($item->title, route('blog.show', $item), $item->seo_title ?: $item->title.' — Mwanafunzi Investor', $item->seo_description ?: $item->excerpt, $item->robots, $item->canonical_url ?: route('blog.show', $item), $item->primary_keyword, route('admin.articles.edit', $item), 'Blog article', $item->featured_image));
        Service::active()->get()->each(fn ($item) => $add($item->title, route('services.show', $item), $item->seo_title ?: $item->title.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_description, $item->robots, $item->canonical_url ?: route('services.show', $item), $item->primary_keyword, route('admin.services.edit', $item), 'Service', $item->featured_image));
        Project::published()->get()->each(fn ($item) => $add($item->title, route('projects.show', $item), $item->seo_title ?: $item->title.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_description, $item->robots, $item->canonical_url ?: route('projects.show', $item), $item->primary_keyword, route('admin.projects.edit', $item), 'Project / case study', $item->featured_image));
        TeamMember::active()->get()->each(fn ($item) => $add($item->name, route('team.show', $item->slug), $item->seo_title ?: $item->name.' — Mwanafunzi Investor', $item->seo_description ?: $item->short_intro, $item->robots, $item->canonical_url ?: route('team.show', $item->slug), $item->primary_keyword, route('admin.team.edit', $item), 'Team profile', $item->portrait));

        $pages = $pages->unique('url')->values()->map(function (array $page): array {
            $page['canonicalConflict'] = filled($page['canonical']) && rtrim($page['canonical'], '/') !== rtrim($page['expectedCanonical'], '/');
            $media = filled($page['image']) ? Media::where('path', $page['image'])->first() : null;
            $page['issues'] = collect([blank($page['title']) ? 'Missing SEO title' : null, blank($page['description']) ? 'Missing meta description' : null, blank($page['keyword']) ? 'No primary keyword target' : null, $page['canonicalConflict'] ? 'Canonical differs from public URL' : null, $media && blank($media->alt_text) ? 'Featured image needs alt text' : null])->filter()->values()->all();
            return $page;
        });

        $indexable = $pages->filter(fn (array $page) => ! str_contains($page['robots'], 'noindex'));
        $titles = $indexable->pluck('title')->filter()->map(fn ($title) => mb_strtolower(trim($title)));
        $duplicateTitleCount = $titles->count() - $titles->unique()->count();
        $descriptions = $indexable->pluck('description')->filter()->map(fn ($description) => mb_strtolower(trim($description)));
        $duplicateDescriptionCount = $descriptions->count() - $descriptions->unique()->count();
        $issues = collect();
        foreach ($pages as $page) {
            foreach ($page['issues'] as $issue) {
                $issues->push(['label' => $page['label'], 'type' => $issue, 'editUrl' => $page['editUrl'], 'url' => $page['url']]);
            }
        }
        $keywordMap = collect(Seo::keywordMap())->map(fn (array $target, string $key) => ['page' => str($key)->headline()->toString(), ...$target])->values();

        return view('admin.seo.index', ['pages' => $pages, 'issues' => $issues, 'keywordMap' => $keywordMap, 'indexableCount' => $indexable->count(), 'missingTitleCount' => $indexable->filter(fn (array $page) => blank($page['title']))->count(), 'missingDescriptionCount' => $indexable->filter(fn (array $page) => blank($page['description']))->count(), 'duplicateTitleCount' => max(0, $duplicateTitleCount), 'duplicateDescriptionCount' => max(0, $duplicateDescriptionCount), 'canonicalConflictCount' => $pages->where('canonicalConflict', true)->count(), 'missingKeywordCount' => $indexable->filter(fn (array $page) => blank($page['keyword']))->count(), 'missingAltCount' => Media::whereNull('alt_text')->orWhere('alt_text', '')->count(), 'noindexCount' => $pages->filter(fn (array $page) => str_contains($page['robots'], 'noindex'))->count(), 'sitemapUrl' => route('sitemap'), 'productionSitemapUrl' => Seo::productionSitemapUrl()]);
    }
}
