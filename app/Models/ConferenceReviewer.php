<?php

namespace App\Models;

use App\Enum\ConferenceReviewerStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConferenceReviewer extends Pivot
{
    protected $table = 'conference_reviewers';
    protected $guarded = ['id'];

    public $incrementing = true;

    protected $casts = [
        'status' => ConferenceReviewerStatus::class,
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
