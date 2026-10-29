<?php

namespace App\Entity;

use App\Enum\StatutExercice;
use App\Repository\ExerciceRepository;
use App\Trait\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Exercice
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateLimitePret = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateRetribution = null;

    #[ORM\Column(type: 'string', enumType: StatutExercice::class)]
    private StatutExercice $statut = StatutExercice::EN_COURS;

    #[ORM\ManyToOne(inversedBy: 'exercices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupement $groupement = null;

    public function __construct()
    {
        $this->initializeDateTimes();
        $this->dateDebut = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin === null ? null : ($dateFin instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateFin) : new \DateTime($dateFin->format('Y-m-d'), $dateFin->getTimezone()));

        return $this;
    }

    public function getDateLimitePret(): ?\DateTimeInterface
    {
        return $this->dateLimitePret;
    }

    public function setDateLimitePret(?\DateTimeInterface $dateLimitePret): self
    {
        $this->dateLimitePret = $dateLimitePret === null ? null : ($dateLimitePret instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateLimitePret) : new \DateTime($dateLimitePret->format('Y-m-d'), $dateLimitePret->getTimezone()));

        return $this;
    }

    public function getDateRetribution(): ?\DateTimeInterface
    {
        return $this->dateRetribution;
    }

    public function setDateRetribution(?\DateTimeInterface $dateRetribution): self
    {
        $this->dateRetribution = $dateRetribution === null ? null : ($dateRetribution instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateRetribution) : new \DateTime($dateRetribution->format('Y-m-d'), $dateRetribution->getTimezone()));

        return $this;
    }

    public function getStatut(): StatutExercice
    {
        return $this->statut;
    }

    public function setStatut(StatutExercice $statut): self
    {
        $this->statut = $statut;

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

        $this->groupement?->getExercices()->removeElement($this);
        $this->groupement = $groupement;
        if (!$groupement->getExercices()->contains($this)) {
            $groupement->getExercices()->add($this);
        }

        return $this;
    }
}
