<?php

namespace App\Models;

use Database\Factories\ConferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
