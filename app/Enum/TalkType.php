<?php

namespace App\Enum;

enum TalkType: string
{
    case LIGHTNING = 'lightning';
    case STANDARD = 'standard';
    case KEYNOTE = 'keynote';

    public function label(): string
    {
        return match ($this) {
            self::LIGHTNING => 'Lightning',
            self::STANDARD => 'Standard',
            self::KEYNOTE => 'Keynote',
        };
    }
}
