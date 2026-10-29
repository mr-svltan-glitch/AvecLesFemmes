<?php

namespace App\DataFixtures;

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
use App\Entity\Tontine;
use App\Entity\TypeCampagne;
use App\Enum\StatutExercice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groupements = [
            ['nom' => 'Groupement Modibo', 'telephone' => '771234567', 'adresse' => 'Dakar', 'statut' => 'actif'],
            ['nom' => 'Groupement Laye', 'telephone' => '781234567', 'adresse' => 'Saint-Louis', 'statut' => 'actif'],
        ];

        $typeCampagne = new TypeCampagne();
        $typeCampagne->setNom('Épargne');
        $typeCampagne->setDescription('Campagne d’épargne');
        $manager->persist($typeCampagne);

        foreach ($groupements as $groupementData) {
            $groupement = new Groupement();
            $groupement->setNom($groupementData['nom']);
            $groupement->setTelephone($groupementData['telephone']);
            $groupement->setAdresse($groupementData['adresse']);
            $groupement->setStatut($groupementData['statut']);
            $manager->persist($groupement);

            $rolePresident = new Role();
            $rolePresident->setNom('Président');
            $rolePresident->setGroupement($groupement);
            $manager->persist($rolePresident);

            $roleTresorier = new Role();
            $roleTresorier->setNom('Trésorier');
            $roleTresorier->setGroupement($groupement);
            $manager->persist($roleTresorier);

            $admin = new Admin();
            $admin->setNomComplet($groupementData['nom'] . ' Admin');
            $admin->setTelephone(substr($groupementData['telephone'], 0, 9) . '00');
            $admin->setAdresse($groupementData['adresse']);
            $admin->setLogin(strtolower(str_replace(' ', '', $groupementData['nom'])) . '_admin');
            $admin->setPassword('secret');
            $admin->setGroupement($groupement);
            $admin->setRole($rolePresident);
            $manager->persist($admin);

            $membre1 = new Membre();
            $membre1->setNomComplet('Membre Un');
            $membre1->setTelephone('771111111');
            $membre1->setAdresse($groupementData['adresse']);
            $membre1->setLogin(strtolower(str_replace(' ', '', $groupementData['nom'])) . '_m1');
            $membre1->setPassword('secret');
            $membre1->setGroupement($groupement);
            $membre1->setRole($rolePresident);
            $manager->persist($membre1);

            $membre2 = new Membre();
            $membre2->setNomComplet('Membre Deux');
            $membre2->setTelephone('772222222');
            $membre2->setAdresse('Saint-Louis');
            $membre2->setLogin(strtolower(str_replace(' ', '', $groupementData['nom'])) . '_m2');
            $membre2->setPassword('secret');
            $membre2->setGroupement($groupement);
            $membre2->setRole($roleTresorier);
            $manager->persist($membre2);

            $epargne = new Epargne();
            $epargne->setNom('Épargne Calebasse ' . $groupementData['nom']);
            $epargne->setDateDebut(new \DateTimeImmutable('2026-01-01'));
            $epargne->setDateFin(new \DateTimeImmutable('2026-12-31'));
            $epargne->setMontant(25000);
            $epargne->setTypeCampagne($typeCampagne);
            $epargne->setGroupement($groupement);
            $epargne->setBut('Achat de matériel');
            $epargne->setBenefice(5000);
            $manager->persist($epargne);

            $tontine = new Tontine();
            $tontine->setNom('Tontine Mensuelle ' . $groupementData['nom']);
            $tontine->setDateDebut(new \DateTimeImmutable('2026-02-01'));
            $tontine->setDateFin(new \DateTimeImmutable('2026-12-31'));
            $tontine->setMontant(30000);
            $tontine->setTypeCampagne($typeCampagne);
            $tontine->setGroupement($groupement);
            $tontine->setNumero('T-' . ($groupementData['nom'] === 'Groupement Modibo' ? '001' : '002'));
            $tontine->setClient('Client Tontine ' . $groupementData['nom']);
            $manager->persist($tontine);

            $exercice = new Exercice();
            $exercice->setDateDebut(new \DateTimeImmutable('2026-01-01'));
            $exercice->setDateFin(new \DateTimeImmutable('2026-12-31'));
            $exercice->setDateLimitePret(new \DateTimeImmutable('2026-06-30'));
            $exercice->setDateRetribution(new \DateTimeImmutable('2026-12-15'));
            $exercice->setStatut(StatutExercice::EN_COURS);
            $exercice->setGroupement($groupement);
            $manager->persist($exercice);

            $mode1 = new ModeCotisation();
            $mode1->setIsLibre(false);
            $mode1->setMembre($membre1);
            $mode1->setCampagne($epargne);
            $manager->persist($mode1);

            $mode2 = new ModeCotisation();
            $mode2->setIsLibre(true);
            $mode2->setMembre($membre2);
            $mode2->setCampagne($epargne);
            $manager->persist($mode2);

            $seance = new Seance();
            $seance->setDateSeance(new \DateTimeImmutable('2026-03-08'));
            $seance->setFrequence('Mensuelle');
            $seance->setStatut('ouverte');
            $seance->setCampagne($epargne);
            $seance->setAdmin($admin);
            $manager->persist($seance);

            $cotisation1 = new Cotisation();
            $cotisation1->setMontant(25000);
            $cotisation1->setDatePaiement(new \DateTimeImmutable('2026-03-08 10:00:00'));
            $cotisation1->setStatut('cotisation');
            $cotisation1->setModeCotisation($mode1);
            $cotisation1->setSeance($seance);
            $manager->persist($cotisation1);

            $cotisation2 = new Cotisation();
            $cotisation2->setMontant(2000);
            $cotisation2->setDatePaiement(new \DateTimeImmutable('2026-03-08 10:30:00'));
            $cotisation2->setStatut('amende');
            $cotisation2->setModeCotisation($mode2);
            $cotisation2->setSeance($seance);
            $manager->persist($cotisation2);

            $cotisation3 = new Cotisation();
            $cotisation3->setMontant(1500);
            $cotisation3->setDatePaiement(new \DateTimeImmutable('2026-03-08 11:00:00'));
            $cotisation3->setStatut('caisse_sociale');
            $cotisation3->setModeCotisation($mode1);
            $cotisation3->setSeance($seance);
            $manager->persist($cotisation3);
        }

        $manager->flush();
    }
}
