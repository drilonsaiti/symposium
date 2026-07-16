<?php

namespace App\Models;

use App\Enum\ConferenceUserStatus;
use Database\Factories\ConferenceFactory;
use Illuminate\Database\Eloquent\Builder;
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
        return $this->belongsTo(User::class)
            ->using(ConferenceUser::class)
            ->withPivot('status');
    }

    public function conferenceUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ConferenceUser::class)
            ->withPivot('status');
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
        return $query->where('ends_at', '<', now()->startOfDay());
    }

    public function scopeCfpOpen($query)
    {
        $now = now();

        return $query
            ->where('cfp_starts_at', '<=', $now)
            ->where('cfp_ends_at', '>=', $now);
    }

    public function scopeCfpUpcoming($query)
    {
        return $query->where('cfp_starts_at', '>', now());
    }

    public function scopeCfpClosed($query)
    {
        return $query->where('cfp_ends_at', '<', now());
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->conferenceUser()
            ->wherePivot(
                'status',
                ConferenceUserStatus::FAVORITED
            );
    }

    public function dismissedByUsers(): BelongsToMany
    {
        return $this->conferenceUser()
            ->wherePivot(
                'status',
                ConferenceUserStatus::DISMISSED
            );
    }


    public function scopeNotDismissedBy(Builder $query,User $user)
    {
        return $query->whereDoesntHave('dismissedByUsers', fn (Builder $q) =>
            $q->where('user_id', $user->id)
        );
    }
}
