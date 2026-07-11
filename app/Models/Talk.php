<?php

namespace App\Models;

use App\Enum\TalkType;
use Database\Factories\TalkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class,'user_id');
    }
}
