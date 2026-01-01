<?php

namespace App\Domain\ContentExtraction\Jobs;

use PageExtraction;
use PageExtractionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Domain\ContentExtraction\Services\PageFetcher;
use App\Domain\ContentExtraction\Services\RunFinalizer;
use App\Domain\ContentExtraction\Services\Extractor\PageContentExtractorResolver;

class ExtractPageContentJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly string $pageExtractionId
    ) {}

    public function handle(
        PageFetcher $fetcher,
        PageContentExtractorResolver $resolver,
        RunFinalizer $finalizer
    ): void {
        $pe = PageExtraction::find($this->pageExtractionId);

        if (! $pe || $pe->status->isFinal()) {
            return;
        }

        $run = $pe->run;

        if ($run->status->isTerminal()) {
            $this->markSkipped($pe, $finalizer);
            return;
        }

        $this->transition($pe, PageExtractionStatus::Fetching);

        $html = $fetcher->fetch($pe->page);

        $this->transition($pe, PageExtractionStatus::Extracting);

        $extractor = $resolver->resolve($run);
        $content = $extractor->extract($html, $pe->page);

        $this->transition($pe, PageExtractionStatus::Storing);

        // Single-statement page update
        $pe->page->update([
            'content' => $content->toArray(),
            'content_hash' => $content->hash(),
            'content_extracted_at' => now(),
        ]);

        $this->markDone($pe, $finalizer);
    }

    protected function transition(PageExtraction $pe, PageExtractionStatus $to): void
    {
        PageExtraction::where('id', $pe->id)
            ->where('status', $pe->status)
            ->update([
                'status' => $to,
                'started_at' => $pe->started_at ?? now(),
            ]);

        $pe->refresh();
    }

    protected function markDone(PageExtraction $pe, RunFinalizer $finalizer): void
    {
        $updated = PageExtraction::where('id', $pe->id)
            ->whereNotIn('status', [
                PageExtractionStatus::Done,
                PageExtractionStatus::Failed,
                PageExtractionStatus::Skipped,
            ])
            ->update([
                'status' => PageExtractionStatus::Done,
                'finished_at' => now(),
            ]);

        if ($updated > 0) {
            $finalizer->pageReachedFinalState($pe->refresh());
        }
    }

    protected function markSkipped(PageExtraction $pe, RunFinalizer $finalizer): void
    {
        $updated = PageExtraction::where('id', $pe->id)
            ->where('status', PageExtractionStatus::Pending)
            ->update([
                'status' => PageExtractionStatus::Skipped,
                'finished_at' => now(),
            ]);

        if ($updated > 0) {
            $finalizer->pageReachedFinalState($pe->refresh());
        }
    }
}
