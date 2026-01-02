<?php

namespace App\Domain\ContentExtraction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PageContent extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'page_id',
        'extractor_version',
        'content',
        'extracted_at',
    ];

    protected $casts = [
        'content' => 'array',
        'extracted_at' => 'datetime',
    ];
}
