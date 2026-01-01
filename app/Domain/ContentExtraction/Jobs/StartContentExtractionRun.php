<?php

namespace App\Domain\ContentExtraction\Jobs;

use PageExtraction;
use App\Models\Page;
use ContentExtractionRun;
use PageExtractionStatus;
use Illuminate\Bus\Queueable;
use ContentExtractionRunStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartContentExtractionRun implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public readonly string $runId
    ) {}

    public function handle(): void
    {
        $run = ContentExtractionRun::find($this->runId);

        if (! $run || $run->status !== ContentExtractionRunStatus::Pending) {
            return;
        }

        Page::where('website_id', $run->website_id)
            ->select('id')
            ->chunkById(500, function ($pages) use ($run) {
                foreach ($pages as $page) {
                    PageExtraction::firstOrCreate(
                        [
                            'run_id' => $run->id,
                            'page_id' => $page->id,
                        ],
                        [
                            'status' => PageExtractionStatus::Pending,
                            'extractor_version' => $run->extractor_version,
                        ]
                    );
                }
            });

        $total = PageExtraction::where('run_id', $run->id)->count();

        ContentExtractionRun::where('id', $run->id)
            ->where('status', ContentExtractionRunStatus::Pending)
            ->update([
                'status' => ContentExtractionRunStatus::Running,
                'total_pages' => $total,
                'started_at' => now(),
            ]);

        PageExtraction::where('run_id', $run->id)
            ->select('id')
            ->each(
                fn($pe) =>
                ExtractPageContentJob::dispatch($pe->id)
                    ->onQueue('page-extraction')
            );
    }
}
