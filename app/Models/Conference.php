<?php

namespace App\Models;

use Database\Factories\ConferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    /** @use HasFactory<ConferenceFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
