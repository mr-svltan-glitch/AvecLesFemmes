<?php

namespace App\Dto;

final class ExerciceDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $dateDebut,
        public readonly ?string $dateFin,
        public readonly ?string $dateLimitePret,
        public readonly ?string $dateRetribution,
        public readonly string $statut,
        public readonly string $statutLabel,
    ) {
    }
}
