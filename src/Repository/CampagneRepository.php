<?php

namespace App\Repository;

use App\Entity\Campagne;
use App\Entity\Groupement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campagne>
 */
class CampagneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campagne::class);
    }

    /**
     * @return Campagne[]
     */
    public function findByGroupement(Groupement $groupement): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.groupement = :groupement')
            ->setParameter('groupement', $groupement)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
