<?php

enum ContentExtractionRunStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case CompletedWithErrors = 'completed_with_errors';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::CompletedWithErrors,
            self::Failed,
            self::Cancelled,
        ]);
    }
}
