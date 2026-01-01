<?php

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PageExtraction extends Model
{
    use HasUlids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'status' => PageExtractionStatus::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function run()
    {
        return $this->belongsTo(ContentExtractionRun::class, 'run_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function transition(PageExtractionStatus $to): void
    {
        if ($this->status->isFinal()) {
            return;
        }

        $this->update([
            'status' => $to,
            'started_at' => $this->started_at ?? now(),
        ]);
    }
}
