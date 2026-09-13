<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->slug = static::generateUniqueSlug($model->name);
        });

    }

    public function talks(): MorphToMany
    {
        return $this->morphedByMany(Talk::class, 'taggable');
    }

    public function conferences(): MorphToMany
    {
        return $this->morphedByMany(Conference::class, 'taggable');
    }

    public function bios(): MorphToMany
    {
        return $this->morphedByMany(Bio::class, 'taggable');
    }

    private static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        if (static::where('slug', $slug)->exists()) {
            return $slug . '-' . Str::random(4);
        }

        return $slug;
    }
}
