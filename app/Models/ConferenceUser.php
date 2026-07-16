<?php

namespace App\Models;

use App\Enum\ConferenceUserStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConferenceUser extends Pivot
{
    //
    protected $table = 'conference_user';
    protected $guarded = ['id'];

    public $incrementing = true;
    protected $casts = [
        'status' => ConferenceUserStatus::class,
    ];


}
