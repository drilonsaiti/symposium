<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalkRevision extends Model
{
    //
    use HasFactory;
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::created(function (TalkRevision $revision) {
            $revision->talk->searchable();
        });
    }
    public function talk()
    {
        return $this->belongsTo(Talk::class);
    }
}
