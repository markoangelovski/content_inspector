<?php

namespace App\Domain\ContentExtraction\Services\Extractor;

use App\Models\Page;
use App\Domain\ContentExtraction\Services\Extractor\ExtractedContent;

interface PageContentExtractor
{
    public function extract(string $html, Page $page): ExtractedContent;
}
