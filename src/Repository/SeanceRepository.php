<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function getMontantTotalCollecte(Seance $seance): float
    {
        $total = 0.0;
        foreach ($seance->getCotisations() as $cotisation) {
            $total += $cotisation->getMontant();
        }

        return $total;
    }

    public function getMontantTotalAmendes(Seance $seance): float
    {
        $total = 0.0;
        foreach ($seance->getCotisations() as $cotisation) {
            if ($cotisation->isAmende()) {
                $total += $cotisation->getMontant();
            }
        }

        return $total;
    }

    public function getMontantTotalCaisseSociale(Seance $seance): float
    {
        $total = 0.0;
        foreach ($seance->getCotisations() as $cotisation) {
            if ($cotisation->isCaisseSociale()) {
                $total += $cotisation->getMontant();
            }
        }

        return $total;
    }
}
