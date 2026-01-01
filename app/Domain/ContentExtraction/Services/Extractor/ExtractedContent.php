<?php

namespace App\Domain\ContentExtraction\Services\Extractor;

class ExtractedContent
{
    public function __construct(
        protected array $data
    ) {}

    public function toArray(): array
    {
        return $this->data;
    }

    public function hash(): string
    {
        return hash('sha256', json_encode($this->data));
    }
}
