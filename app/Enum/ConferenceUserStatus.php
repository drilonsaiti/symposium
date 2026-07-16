<?php

namespace App\Enum;

enum ConferenceUserStatus: string
{
    case FAVORITED = 'favorited';
    case DISMISSED = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::FAVORITED => 'Favorited',
            self::DISMISSED => 'Dismissed',
        };
    }

}
