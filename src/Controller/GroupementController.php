<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\Groupement;
use App\Entity\Seance;
use App\Service\CampagneService;
use App\Service\GroupementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupementController extends AbstractController
{
    public function __construct(
        private readonly GroupementService $groupementService,
        private readonly CampagneService $campagneService,
    ) {
    }

    #[Route('/', name: 'groupements_index')]
    public function index(): Response
    {
        return $this->render('groupement/index.html.twig', [
            'groupements' => $this->groupementService->getGroupementsAvecExerciceEnCours(),
        ]);
    }

    #[Route('/groupements/{id}', name: 'groupement_show', requirements: ['id' => '\\d+'])]
    public function show(Groupement $groupement): Response
    {
        $campagnes = $this->campagneService->getCampagnesPourGroupement($groupement);
        $exercice = $this->groupementService->getExerciceEnCours($groupement);

        return $this->render('groupement/show.html.twig', [
            'groupement' => $groupement,
            'campagnes' => $campagnes,
            'exercice' => $exercice,
        ]);
    }

    #[Route('/campagnes/{id}', name: 'campagne_show', requirements: ['id' => '\\d+'])]
    public function showCampagne(Campagne $campagne): Response
    {
        $detail = $this->campagneService->getDetailCampagne($campagne);

        return $this->render('campagne/show.html.twig', [
            'campagne' => $campagne,
            'membres' => $detail['membres'],
            'seances' => $detail['seances'],
        ]);
    }

    #[Route('/seances/{id}', name: 'seance_show', requirements: ['id' => '\\d+'])]
    public function showSeance(Seance $seance): Response
    {
        $detail = $this->campagneService->getDetailSeance($seance);

        return $this->render('seance/show.html.twig', [
            'seance' => $seance,
            'detail' => $detail,
            'montant_total_collecte' => $detail->montantTotalCollecte,
            'montant_total_amendes' => $detail->montantTotalAmendes,
            'montant_total_caisse_sociale' => $detail->montantTotalCaisseSociale,
            'cotisations' => $detail->cotisations,
        ]);
    }
}
