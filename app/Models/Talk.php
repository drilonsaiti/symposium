<?php

namespace App\Models;

use App\Enum\TalkType;
use Database\Factories\TalkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Talk extends Model
{
    /** @use HasFactory<TalkFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => TalkType::class,
    ];

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
}
