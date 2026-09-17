<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfpAnswer extends Model
{
    protected $guarded = ['id'];


    public function question(): BelongsTo
    {
        return $this->belongsTo(CfpQuestion::class);
    }

    public function conferenceTalk(): BelongsTo
    {
        return $this->belongsTo(ConferenceTalk::class);
    }
}
