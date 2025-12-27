<?php

namespace App\Jobs;

use Throwable;
use App\Models\Sitemap;
use App\Models\Website;
use Illuminate\Support\Str;
use App\Services\SitemapsFetcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSitemaps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $websiteId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $websiteId)
    {
        $this->websiteId = $websiteId;
    }

    /**
     * Execute the job.
     */
    public function handle(SitemapsFetcher $fetcher): void
    {
        $website = Website::find($this->websiteId);

        // Website might have been deleted
        if ($website === null) {
            return;
        }

        if (! $website->sitemaps_processing) {
            return;
        }

        $sitemaps = collect($fetcher->fetch($website->url));

        $now = now();

        $rows = $sitemaps->map(fn(string $url) => [
            'id' => strtolower(Str::ulid()),
            'website_id' => $website->id,
            'url' => $url,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->all();

        DB::transaction(function () use ($website, $rows, $now) {
            Sitemap::insertOrIgnore($rows);

            $website->update([
                'sitemaps_fetched' => true,
                'sitemaps_count' => count($rows),
                'sitemaps_last_sync' => $now,
                'sitemaps_message' => 'ok',
                'sitemaps_processing' => false,
            ]);
        });
    }

    public function failed(Throwable $e): void
    {
        if ($website = Website::find($this->websiteId)) {
            $website->update([
                'sitemaps_processing' => false,
                'sitemaps_last_sync' => now(),
                'sitemaps_message' => $e->getMessage(),
            ]);
        }
    }
}
