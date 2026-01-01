<?php

namespace App\Domain\ContentExtraction\Services\Extractor;

use ContentExtractionRun;

class PageContentExtractorResolver
{
    public function resolve(ContentExtractionRun $run): PageContentExtractor
    {
        return match ($run->extractor_version) {
            'readability-v1' => new ReadabilityExtractor(),
            default => new ReadabilityExtractor(),
        };
    }
}
