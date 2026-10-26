<?php

namespace App\Entity;

use App\Repository\ModeCotisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModeCotisationRepository::class)]
class ModeCotisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $isLibre = false;

    #[ORM\ManyToOne(inversedBy: 'modeCotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'modeCotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    /**
     * @var Collection<int, Cotisation>
     */
    #[ORM\OneToMany(mappedBy: 'modeCotisation', targetEntity: Cotisation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $cotisations;

    public function __construct()
    {
        $this->cotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLibre(): bool
    {
        return $this->isLibre;
    }

    public function setIsLibre(bool $isLibre): self
    {
        $this->isLibre = $isLibre;

        return $this;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): self
    {
        $this->membre = $membre;

        return $this;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): self
    {
        $this->campagne = $campagne;

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }
}
