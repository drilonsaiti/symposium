<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class Bio extends Model
{
    //
    use Searchable;
    protected $guarded = ['id'];


    protected static function booted(): void
    {
        static::deleting(fn ($bio) => $bio->tags()->detach());
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'bio' => $this->bio,
            'user_id' => $this->user_id,
        ];
    }

}
