<?php

namespace App\Domain\ContentExtraction\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Http;

class PageFetcher
{
    public function fetch(Page $page): string
    {
        $response = Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'ContentExtractorBot/1.0',
            ])
            ->get($page->url);

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Failed fetching {$page->url} ({$response->status()})"
            );
        }

        return $response->body();
    }
}
