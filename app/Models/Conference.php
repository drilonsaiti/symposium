<?php

namespace App\Models;

use Database\Factories\ConferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    /** @use HasFactory<ConferenceFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'cfp_starts_at' => 'date',
        'cfp_ends_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class)
            ->using(ConferenceTalk::class)
            ->withPivot([
                'status',
                'bio_id'
            ])
            ->withTimestamps();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now()->startOfDay());
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now()->startOfDay());
    }

    public function scopeCfpOpen($query)
    {
        return $query->where('cfp_starts_at', '<=', now()->startOfDay())
            ->where('cfp_ends_at', '>=', now()->endOfDay());
    }

    public function scopeCfpUpcoming($query)
    {
        return $query->where('cfp_starts_at', '>=', now()->startOfDay());
    }

    public function scopeCfpClosed($query)
    {
        return $query->where('cfp_ends_at', '<=', now()->endOfDay());
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }
}
