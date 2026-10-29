<?php

namespace App\Dto;

final class SeanceDetailDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $dateSeance,
        public readonly string $frequence,
        public readonly string $statut,
        public readonly float $montantTotalCollecte,
        public readonly float $montantTotalAmendes,
        public readonly float $montantTotalCaisseSociale,
        public readonly array $cotisations,
        public readonly array $membres,
    ) {
    }
}
