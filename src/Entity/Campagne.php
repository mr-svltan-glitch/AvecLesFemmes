<?php

namespace App\Entity;

use App\Repository\CampagneRepository;
use App\Trait\DateTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CampagneRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['campagne' => Campagne::class, 'epargne' => Epargne::class, 'tontine' => Tontine::class])]
#[ORM\HasLifecycleCallbacks]
class Campagne
{
    use DateTimeTrait;

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

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?TypeCampagne $typeCampagne = null;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Groupement $groupement = null;

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: Seance::class, cascade: ['persist'])]
    protected Collection $seances;

    /**
     * @var Collection<int, ModeCotisation>
     */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: ModeCotisation::class, cascade: ['persist'])]
    protected Collection $modeCotisations;

    public function __construct()
    {
        $this->initializeDateTimes();
        $this->seances = new ArrayCollection();
        $this->modeCotisations = new ArrayCollection();
        $this->dateDebut = new \DateTime();
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
        $this->dateDebut = $dateDebut instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateDebut) : new \DateTime($dateDebut->format('Y-m-d'), $dateDebut->getTimezone());

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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin === null ? null : ($dateFin instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateFin) : new \DateTime($dateFin->format('Y-m-d'), $dateFin->getTimezone()));

        return $this;
    }

    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
    }

    public function setTypeCampagne(TypeCampagne $typeCampagne): self
    {
        if ($this->typeCampagne === $typeCampagne) {
            return $this;
        }

        $this->typeCampagne?->getCampagnes()->removeElement($this);
        $this->typeCampagne = $typeCampagne;
        if (!$typeCampagne->getCampagnes()->contains($this)) {
            $typeCampagne->getCampagnes()->add($this);
        }

        return $this;
    }

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(Groupement $groupement): self
    {
        if ($this->groupement === $groupement) {
            return $this;
        }

        $this->groupement?->getCampagnes()->removeElement($this);
        $this->groupement = $groupement;
        if (!$groupement->getCampagnes()->contains($this)) {
            $groupement->getCampagnes()->add($this);
        }

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
        $seance->setCampagne($this);

        return $this;
    }

    /**
     * @return Collection<int, ModeCotisation>
     */
    public function getModeCotisations(): Collection
    {
        return $this->modeCotisations;
    }

    public function addModeCotisation(ModeCotisation $modeCotisation): self
    {
        $modeCotisation->setCampagne($this);

        return $this;
    }
}
