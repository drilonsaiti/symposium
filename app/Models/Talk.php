<?php

namespace App\Models;

use App\Enum\TalkType;
use Database\Factories\TalkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class Talk extends Model
{
    /** @use HasFactory<TalkFactory> */
    use HasFactory,Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => TalkType::class,
    ];

    protected static function booted(): void
    {
        static::deleting(fn ($talk) => $talk->tags()->detach());
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class)
            ->using(ConferenceTalk::class)
            ->withPivot([
                'status',
                'bio_id'
            ])
            ->withTimestamps();
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(TalkRevision::class);
    }

    public function currentRevision(): HasOne
    {
        return $this->hasOne(TalkRevision::class)->latestOfMany();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('currentRevision');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'abstract' => $this->currentRevision?->abstract,
            'type' => $this->type->value,
            'user_id' => $this->user_id,
        ];
    }
}
