<?php

namespace App\Enum;

enum QuestionType: string
{
    case FREE_TEXT = 'free_text';

    public function label(): string
    {
        return match ($this) {
            QuestionType::FREE_TEXT => 'Free text',
        };
    }
}
