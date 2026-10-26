<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CampagneRepository::class)]
class Campagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    protected string $nom;

    #[ORM\Column(type: 'date')]
    protected \DateTimeInterface $dateDebut;

    #[ORM\Column(type: 'date', nullable: true)]
    protected ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'float')]
    protected float $montant;

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $dateCreatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?TypeCampagne $typeCampagne = null;

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: Seance::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $seances;

    /**
     * @var Collection<int, ModeCotisation>
     */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: ModeCotisation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $modeCotisations;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->modeCotisations = new ArrayCollection();
        $this->dateCreatedAt = new \DateTimeImmutable();
        $this->dateDebut = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
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

    public function getDateCreatedAt(): \DateTimeImmutable
    {
        return $this->dateCreatedAt;
    }

    public function setDateCreatedAt(\DateTimeImmutable $dateCreatedAt): self
    {
        $this->dateCreatedAt = $dateCreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
    }

    public function setTypeCampagne(?TypeCampagne $typeCampagne): self
    {
        $this->typeCampagne = $typeCampagne;

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
            $seance->setCampagne($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            if ($seance->getCampagne() === $this) {
                $seance->setCampagne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModeCotisation>
     */
    public function getModeCotisations(): Collection
    {
        return $this->modeCotisations;
    }
}
