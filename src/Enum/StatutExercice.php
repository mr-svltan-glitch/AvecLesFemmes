<?php

namespace App\Enum;

enum StatutExercice: string
{
    case EN_COURS = 'en_cours';
    case CLOTURE = 'cloture';

    public function label(): string
    {
        return match ($this) {
            self::EN_COURS => 'En cours',
            self::CLOTURE => 'Clôturé',
        };
    }
}
