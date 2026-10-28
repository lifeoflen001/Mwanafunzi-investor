<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Preserve the existing Digital Software page while making its existing
     * hospitality destination available through the CMS editor.
     */
    public function up(): void
    {
        $section = Page::query()
            ->where('key', 'development')
            ->first()?->sections()
            ->where('key', 'hospitality')
            ->first();

        if (! $section) {
            return;
        }

        $section->update([
            'cta_label' => $section->cta_label ?: 'Explore hospitality work',
            'cta_url' => $section->cta_url ?: '/digital-software/hospitality',
        ]);
    }

    /**
     * Keep this additive backfill non-destructive on rollback.
     */
    public function down(): void
    {
        // Do not remove administrator content on rollback.
    }
};
