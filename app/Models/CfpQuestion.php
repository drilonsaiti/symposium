<?php

namespace App\Models;

use App\Enum\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CfpQuestion extends Model
{
    //

    protected $guarded = ['id'];

    protected $casts = [
        'type' => QuestionType::class,
        'is_active' => 'boolean',
        'required' => 'boolean'
    ];


    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CfpAnswer::class);
    }

    public function canBeDeleted(): bool
    {
        return !$this->answers()->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
