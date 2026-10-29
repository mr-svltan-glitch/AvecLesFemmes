<?php

namespace App\Service;

use App\Dto\ExerciceDto;
use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Repository\ExerciceRepository;
use App\Repository\GroupementRepository;

final class GroupementService
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
        private readonly ExerciceRepository $exerciceRepository,
    ) {
    }

    /**
     * @return array<int, array{groupement: Groupement, exercice: ?ExerciceDto}>
     */
    public function getGroupementsAvecExerciceEnCours(): array
    {
        $groupements = $this->groupementRepository->findAll();
        $result = [];

        foreach ($groupements as $groupement) {
            $exercice = $this->exerciceRepository->findCurrentByGroupement($groupement);
            $result[] = [
                'groupement' => $groupement,
                'exercice' => $exercice ? new ExerciceDto(
                    $exercice->getId(),
                    $exercice->getDateDebut()->format('d/m/Y'),
                    $exercice->getDateFin()?->format('d/m/Y'),
                    $exercice->getDateLimitePret()?->format('d/m/Y'),
                    $exercice->getDateRetribution()?->format('d/m/Y'),
                    $exercice->getStatut()->value,
                    $exercice->getStatut()->label(),
                ) : null,
            ];
        }

        return $result;
    }

    public function getExerciceEnCours(Groupement $groupement): ?Exercice
    {
        return $this->exerciceRepository->findCurrentByGroupement($groupement);
    }
}
