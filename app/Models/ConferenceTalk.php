<?php

namespace App\Models;

use App\Enum\TalkSubmissionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConferenceTalk extends Pivot
{
    //
    protected $table = 'conference_talk';
    protected $guarded = ['id'];
    protected $casts = [
        'status' => TalkSubmissionStatus::class,
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function talk(): BelongsTo
    {
        return $this->belongsTo(Talk::class);
    }
}
