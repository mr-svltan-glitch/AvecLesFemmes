<?php

namespace App\Repository;

use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Enum\StatutExercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercice>
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }

    public function findCurrentByGroupement(Groupement $groupement): ?Exercice
    {
        return $this->createQueryBuilder('e')
            ->where('e.groupement = :groupement')
            ->andWhere('e.statut = :statut')
            ->setParameter('groupement', $groupement)
            ->setParameter('statut', StatutExercice::EN_COURS)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
