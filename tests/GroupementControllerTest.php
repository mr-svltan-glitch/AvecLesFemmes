<?php

namespace App\Tests\Controller;

use App\Entity\Admin;
use App\Entity\Campagne;
use App\Entity\Cotisation;
use App\Entity\Epargne;
use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Entity\Membre;
use App\Entity\ModeCotisation;
use App\Entity\Role;
use App\Entity\Seance;
use App\Entity\TypeCampagne;
use App\Enum\StatutExercice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GroupementControllerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $_ENV['DATABASE_URL'] = 'sqlite:///:memory:';

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $tool = new SchemaTool($this->entityManager);
        $tool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

        $this->seedData();
    }

    public function testFindCurrentExerciseForGroupement(): void
    {
        $repository = $this->entityManager->getRepository(Exercice::class);
        $groupement = $this->entityManager->getRepository(Groupement::class)->findOneBy(['nom' => 'Groupement A']);

        $this->assertNotNull($groupement);
        $this->assertNotNull($repository->findCurrentByGroupement($groupement));
        $this->assertSame(StatutExercice::EN_COURS, $repository->findCurrentByGroupement($groupement)->getStatut());
    }

    public function testSeanceTotalsAreCalculatedFromCotisations(): void
    {
        $campagne = $this->entityManager->getRepository(Campagne::class)->findOneBy(['nom' => 'Épargne Calebasse']);
        $seance = $this->entityManager->getRepository(Seance::class)->findOneBy(['campagne' => $campagne]);
        $repository = $this->entityManager->getRepository(Seance::class);

        $this->assertNotNull($campagne);
        $this->assertNotNull($seance);
        $this->assertSame(9000.0, $repository->getMontantTotalCollecte($seance));
        $this->assertSame(2500.0, $repository->getMontantTotalAmendes($seance));
        $this->assertSame(1500.0, $repository->getMontantTotalCaisseSociale($seance));
    }

    private function seedData(): void
    {
        $groupement = new Groupement();
        $groupement->setNom('Groupement A');
        $groupement->setTelephone('771234567');
        $groupement->setAdresse('Dakar');
        $groupement->setStatut('actif');
        $this->entityManager->persist($groupement);

        $roleAccepted = new Role();
        $roleAccepted->setNom('Porteur');
        $roleAccepted->setGroupement($groupement);
        $this->entityManager->persist($roleAccepted);

        $admin = new Admin();
        $admin->setNomComplet('Administrateur');
        $admin->setTelephone('770000000');
        $admin->setAdresse('Dakar');
        $admin->setLogin('admin');
        $admin->setPassword('secret');
        $admin->setGroupement($groupement);
        $admin->setRole($roleAccepted);
        $this->entityManager->persist($admin);

        $membre = new Membre();
        $membre->setNomComplet('Membre Test');
        $membre->setTelephone('772222222');
        $membre->setAdresse('Saint-Louis');
        $membre->setLogin('membre');
        $membre->setPassword('secret');
        $membre->setGroupement($groupement);
        $membre->setRole($roleAccepted);
        $this->entityManager->persist($membre);

        $typeCampagne = new TypeCampagne();
        $typeCampagne->setNom('Épargne');
        $typeCampagne->setDescription('Campagne d’épargne');
        $this->entityManager->persist($typeCampagne);

        $campagne = new Epargne();
        $campagne->setNom('Épargne Calebasse');
        $campagne->setDateDebut(new \DateTimeImmutable('2026-01-01'));
        $campagne->setDateFin(new \DateTimeImmutable('2026-12-31'));
        $campagne->setMontant(15000);
        $campagne->setTypeCampagne($typeCampagne);
        $campagne->setGroupement($groupement);
        $campagne->setBut('Achat de matériel');
        $campagne->setBenefice(3000);
        $this->entityManager->persist($campagne);

        $exercice = new Exercice();
        $exercice->setDateDebut(new \DateTimeImmutable('2026-01-01'));
        $exercice->setDateFin(new \DateTimeImmutable('2026-12-31'));
        $exercice->setDateLimitePret(new \DateTimeImmutable('2026-06-30'));
        $exercice->setDateRetribution(new \DateTimeImmutable('2026-12-15'));
        $exercice->setStatut(StatutExercice::EN_COURS);
        $exercice->setGroupement($groupement);
        $this->entityManager->persist($exercice);

        $mode = new ModeCotisation();
        $mode->setIsLibre(false);
        $mode->setMembre($membre);
        $mode->setCampagne($campagne);
        $this->entityManager->persist($mode);

        $seance = new Seance();
        $seance->setDateSeance(new \DateTimeImmutable('2026-03-08'));
        $seance->setFrequence('Mensuelle');
        $seance->setStatut('ouverte');
        $seance->setCampagne($campagne);
        $seance->setAdmin($admin);
        $this->entityManager->persist($seance);

        $cotisationBase = new Cotisation();
        $cotisationBase->setMontant(5000);
        $cotisationBase->setDatePaiement(new \DateTimeImmutable('2026-03-08 09:00:00'));
        $cotisationBase->setStatut('cotisation');
        $cotisationBase->setModeCotisation($mode);
        $cotisationBase->setSeance($seance);
        $this->entityManager->persist($cotisationBase);

        $cotisationAmende = new Cotisation();
        $cotisationAmende->setMontant(2500);
        $cotisationAmende->setDatePaiement(new \DateTimeImmutable('2026-03-08 09:05:00'));
        $cotisationAmende->setStatut('amende journaliere');
        $cotisationAmende->setModeCotisation($mode);
        $cotisationAmende->setSeance($seance);
        $this->entityManager->persist($cotisationAmende);

        $cotisationCaisse = new Cotisation();
        $cotisationCaisse->setMontant(1500);
        $cotisationCaisse->setDatePaiement(new \DateTimeImmutable('2026-03-08 09:10:00'));
        $cotisationCaisse->setStatut('caisse sociale');
        $cotisationCaisse->setModeCotisation($mode);
        $cotisationCaisse->setSeance($seance);
        $this->entityManager->persist($cotisationCaisse);

        $this->entityManager->flush();
    }
}
