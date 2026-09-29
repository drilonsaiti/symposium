<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use App\Enum\ReviewRecommendation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceTalkReview extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'recommendation' => ReviewRecommendation::class,
    ];

    public function conferenceTalk(): BelongsTo
    {
        return $this->belongsTo(ConferenceTalk::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
