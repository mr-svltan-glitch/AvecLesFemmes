<?php

namespace App\Entity;

use App\Repository\CotisationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotisationRepository::class)]
class Cotisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private float $montant;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datePaiement;

    #[ORM\Column(length: 50)]
    private string $statut;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModeCotisation $modeCotisation = null;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seance $seance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): \DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getModeCotisation(): ?ModeCotisation
    {
        return $this->modeCotisation;
    }

    public function setModeCotisation(ModeCotisation $modeCotisation): self
    {
        if ($this->modeCotisation === $modeCotisation) {
            return $this;
        }

        $this->modeCotisation?->getCotisations()->removeElement($this);
        $this->modeCotisation = $modeCotisation;
        if (!$modeCotisation->getCotisations()->contains($this)) {
            $modeCotisation->getCotisations()->add($this);
        }

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(Seance $seance): self
    {
        if ($this->seance === $seance) {
            return $this;
        }

        $this->seance?->getCotisations()->removeElement($this);
        $this->seance = $seance;
        if (!$seance->getCotisations()->contains($this)) {
            $seance->getCotisations()->add($this);
        }

        return $this;
    }
}
