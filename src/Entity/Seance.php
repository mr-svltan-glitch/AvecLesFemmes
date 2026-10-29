<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateSeance;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $frequence;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private string $statut;

    #[ORM\ManyToOne(inversedBy: 'seances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    #[ORM\ManyToOne(inversedBy: 'seances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Admin $admin = null;

    /**
     * @var Collection<int, Cotisation>
     */
    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Cotisation::class, cascade: ['persist'])]
    private Collection $cotisations;

    public function __construct()
    {
        $this->cotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSeance(): \DateTimeInterface
    {
        return $this->dateSeance;
    }

    public function setDateSeance(\DateTimeInterface $dateSeance): self
    {
        $this->dateSeance = $dateSeance instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateSeance) : new \DateTime($dateSeance->format('Y-m-d'), $dateSeance->getTimezone());

        return $this;
    }

    public function getFrequence(): string
    {
        return $this->frequence;
    }

    public function setFrequence(string $frequence): self
    {
        $this->frequence = $frequence;

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

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(Campagne $campagne): self
    {
        if ($this->campagne === $campagne) {
            return $this;
        }

        $this->campagne?->getSeances()->removeElement($this);
        $this->campagne = $campagne;
        if (!$campagne->getSeances()->contains($this)) {
            $campagne->getSeances()->add($this);
        }

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(Admin $admin): self
    {
        if ($this->admin === $admin) {
            return $this;
        }

        $this->admin?->getSeances()->removeElement($this);
        $this->admin = $admin;
        if (!$admin->getSeances()->contains($this)) {
            $admin->getSeances()->add($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }

    public function addCotisation(Cotisation $cotisation): self
    {
        if (!$this->cotisations->contains($cotisation)) {
            $this->cotisations->add($cotisation);
            $cotisation->setSeance($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): self
    {
        if ($this->cotisations->removeElement($cotisation)) {
            $cotisation->setSeance(null);
        }

        return $this;
    }

    public function getMontantTotalCollecte(): float
    {
        $total = 0.0;
        foreach ($this->cotisations as $cotisation) {
            $total += $cotisation->getMontant();
        }

        return $total;
    }

    public function getMontantTotalAmendes(): float
    {
        $total = 0.0;
        foreach ($this->cotisations as $cotisation) {
            if ($cotisation->isAmende()) {
                $total += $cotisation->getMontant();
            }
        }

        return $total;
    }

    public function getMontantTotalCaisseSociale(): float
    {
        $total = 0.0;
        foreach ($this->cotisations as $cotisation) {
            if ($cotisation->isCaisseSociale()) {
                $total += $cotisation->getMontant();
            }
        }

        return $total;
    }
}
