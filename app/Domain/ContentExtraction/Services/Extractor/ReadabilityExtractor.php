<?php

namespace App\Domain\ContentExtraction\Services\Extractor;

use App\Models\Page;

class ReadabilityExtractor implements PageContentExtractor
{
    public function extract(string $html, Page $page): ExtractedContent
    {
        /**
         * NOTE:
         * This is intentionally minimal.
         * You will plug in Readability / DOM logic here later.
         */

        return new ExtractedContent([
            'url' => $page->url,
            'title' => null,
            'content' => [
                'text' => strip_tags($html),
                'html' => null,
            ],
            'extracted_at' => now()->toIso8601String(),
            'extractor_version' => 'readability-v1',
        ]);
    }
}
