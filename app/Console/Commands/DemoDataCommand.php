<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Reports and, only with an explicit flag, removes known local QA fixtures.
 *
 * The selectors are intentionally exact. This command never guesses from a
 * date, status, owner, or free-form text that could also match real content.
 */
class DemoDataCommand extends Command
{
    protected $signature = 'app:demo-data
        {action=report : report or cleanup}
        {--apply : apply the exact cleanup selectors after reviewing the report}
        {--json : print machine-readable output}';

    protected $description = 'Inventory or safely remove explicitly marked local QA fixtures';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing', 'staging'])) {
            $this->error('Demo data management is limited to local, testing, and staging environments.');

            return self::FAILURE;
        }

        $action = strtolower((string) $this->argument('action'));
        if (! in_array($action, ['report', 'cleanup'], true)) {
            $this->error('Action must be report or cleanup.');

            return self::INVALID;
        }

        $records = $this->selectors();
        $report = collect($records)->map(fn (array $record): array => [
            'table' => $record['table'],
            'selector' => $record['selector'],
            'count' => $record['query']()->count(),
        ])->values()->all();

        if ($this->option('json')) {
            $this->line(json_encode(['environment' => app()->environment(), 'records' => $report], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } else {
            $this->info('Exact demo/QA fixture inventory (no records changed):');
            $this->table(['Table', 'Selector', 'Count'], $report);
        }

        if ($action === 'report' || ! $this->option('apply')) {
            if ($action === 'cleanup') {
                $this->warn('Dry run only. Re-run with --apply after reviewing the exact selectors.');
            }

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($records): void {
                foreach ($records as $record) {
                    $record['query']()->delete();
                }
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Cleanup failed; the transaction was rolled back.');

            return self::FAILURE;
        }

        $this->info('Approved exact demo/QA selectors were removed. Re-run the report to verify the result.');

        return self::SUCCESS;
    }

    /**
     * Keep this allowlist narrow enough that a legitimate record cannot match
     * merely because it is old, unpublished, or associated with a test user.
     *
     * @return array<int, array{table:string, selector:string, query:callable}>
     */
    private function selectors(): array
    {
        return [
            ['table' => 'users', 'selector' => 'email IN admin@demo..., client@demo..., demo.admin@..., demo.client@...', 'query' => fn () => DB::table('users')->whereIn('email', [
                'admin@demo.mwanafunziinvestor.test',
                'client@demo.mwanafunziinvestor.test',
                'demo.admin@mwanafunziinvestor.test',
                'demo.client@mwanafunziinvestor.test',
            ])],
            ['table' => 'services', 'selector' => 'slug LIKE demo-%', 'query' => fn () => DB::table('services')->where('slug', 'like', 'demo-%')],
            ['table' => 'projects', 'selector' => 'slug = demo-studio-project', 'query' => fn () => DB::table('projects')->where('slug', 'demo-studio-project')],
            ['table' => 'testimonials', 'selector' => 'client_name LIKE [DEMO]%', 'query' => fn () => DB::table('testimonials')->where('client_name', 'like', '[DEMO]%')],
            ['table' => 'courses', 'selector' => 'slug = demo-course-for-qa', 'query' => fn () => DB::table('courses')->where('slug', 'demo-course-for-qa')],
            ['table' => 'articles', 'selector' => 'slug = demo-article-for-qa', 'query' => fn () => DB::table('articles')->where('slug', 'demo-article-for-qa')],
            ['table' => 'products', 'selector' => 'slug = demo-product-for-qa', 'query' => fn () => DB::table('products')->where('slug', 'demo-product-for-qa')],
            ['table' => 'orders', 'selector' => 'order_number = DEMO-ORDER-0001', 'query' => fn () => DB::table('orders')->where('order_number', 'DEMO-ORDER-0001')],
            ['table' => 'contact_messages', 'selector' => 'email IN demo.enquirer@example.test, phase2-contact@example.test', 'query' => fn () => DB::table('contact_messages')->whereIn('email', [
                'demo.enquirer@example.test',
                'phase2-contact@example.test',
            ])],
            ['table' => 'newsletter_subscribers', 'selector' => 'email IN demo.subscriber@example.test, newsletter-qa@example.test', 'query' => fn () => DB::table('newsletter_subscribers')->whereIn('email', [
                'demo.subscriber@example.test',
                'newsletter-qa@example.test',
            ])],
            ['table' => 'studio_inquiries', 'selector' => 'reference = DEMO-STUDIO-0001', 'query' => fn () => DB::table('studio_inquiries')->where('reference', 'DEMO-STUDIO-0001')],
            ['table' => 'pages', 'selector' => 'key = demo-audit-page', 'query' => fn () => DB::table('pages')->where('key', 'demo-audit-page')],
        ];
    }
}
