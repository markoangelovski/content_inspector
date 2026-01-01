<?php

namespace App\Domain\ContentExtraction\Services;

use PageExtraction;
use ContentExtractionRun;
use PageExtractionStatus;
use ContentExtractionRunStatus;
use Illuminate\Support\Facades\DB;

class RunFinalizer
{
    /**
     * Must be called exactly once when a page reaches a FINAL state.
     */
    public function pageReachedFinalState(PageExtraction $pageExtraction): void
    {
        $runId = $pageExtraction->run_id;

        // 1. Increment counters atomically (single statement)
        ContentExtractionRun::where('id', $runId)->update([
            'processed_pages' => DB::raw('processed_pages + 1'),
            'failed_pages' => $pageExtraction->status === PageExtractionStatus::Failed
                ? DB::raw('failed_pages + 1')
                : DB::raw('failed_pages'),
        ]);

        // 2. Attempt finalization (compare-and-set)
        $this->attemptFinalizeRun($runId);
    }

    /**
     * Attempts to finalize the run.
     * Safe to call concurrently.
     */
    protected function attemptFinalizeRun(string $runId): void
    {
        $run = ContentExtractionRun::find($runId);

        if (! $run) {
            return;
        }

        // Already terminal → nothing to do
        if ($run->status->isTerminal()) {
            return;
        }

        if ($run->processed_pages < $run->total_pages) {
            return;
        }

        $finalStatus = $run->failed_pages > 0
            ? ContentExtractionRunStatus::CompletedWithErrors
            : ContentExtractionRunStatus::Completed;

        // Compare-and-set to avoid race conditions
        ContentExtractionRun::where('id', $run->id)
            ->where('status', ContentExtractionRunStatus::Running)
            ->update([
                'status' => $finalStatus,
                'finished_at' => now(),
            ]);
    }
}
