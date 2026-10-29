<?php

namespace App\Service;

use App\Dto\SeanceDetailDto;
use App\Entity\Campagne;
use App\Entity\Groupement;
use App\Entity\Seance;
use App\Repository\CampagneRepository;
use App\Repository\SeanceRepository;

final class CampagneService
{
    public function __construct(
        private readonly CampagneRepository $campagneRepository,
        private readonly SeanceRepository $seanceRepository,
    ) {
    }

    /**
     * @return Campagne[]
     */
    public function getCampagnesPourGroupement(Groupement $groupement): array
    {
        return $this->campagneRepository->findByGroupement($groupement);
    }

    public function getDetailCampagne(Campagne $campagne): array
    {
        $membres = $campagne->getModeCotisations()->map(fn ($mode) => $mode->getMembre())->toArray();
        $seances = $campagne->getSeances()->toArray();

        return [
            'membres' => array_values($membres),
            'seances' => $seances,
        ];
    }

    public function getDetailSeance(Seance $seance): SeanceDetailDto
    {
        return new SeanceDetailDto(
            $seance->getId() ?? 0,
            $seance->getDateSeance()->format('d/m/Y'),
            $seance->getFrequence(),
            $seance->getStatut(),
            $this->seanceRepository->getMontantTotalCollecte($seance),
            $this->seanceRepository->getMontantTotalAmendes($seance),
            $this->seanceRepository->getMontantTotalCaisseSociale($seance),
            $seance->getCotisations()->toArray(),
            $seance->getCampagne()?->getModeCotisations()->map(static fn ($mode) => $mode->getMembre())->toArray() ?? [],
        );
    }
}
