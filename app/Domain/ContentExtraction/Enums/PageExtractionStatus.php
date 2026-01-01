<?php

enum PageExtractionStatus: string
{
    case Pending = 'pending';
    case Fetching = 'fetching';
    case Fetched = 'fetched';
    case Extracting = 'extracting';
    case Storing = 'storing';
    case Done = 'done';
    case Failed = 'failed';
    case Skipped = 'skipped';

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Done,
            self::Failed,
            self::Skipped,
        ]);
    }
}
