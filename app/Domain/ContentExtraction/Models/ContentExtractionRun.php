<?php

use App\Models\Website;
use ContentExtractionRunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ContentExtractionRun extends Model
{
    use HasUlids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'status' => ContentExtractionRunStatus::class,
        'config' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function pageExtractions()
    {
        return $this->hasMany(PageExtraction::class, 'run_id');
    }

    public function markRunning(int $totalPages): void
    {
        $this->update([
            'status' => ContentExtractionRunStatus::Running,
            'total_pages' => $totalPages,
            'started_at' => now(),
        ]);
    }
}
