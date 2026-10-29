<?php

namespace App\Entity;

use App\Repository\GroupementRepository;
use App\Trait\DateTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Groupement
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $nom;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50)]
    private string $statut = 'actif';

    /**
     * @var Collection<int, Membre>
     */
    #[ORM\OneToMany(mappedBy: 'groupement', targetEntity: Membre::class, cascade: ['persist'])]
    private Collection $membres;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\OneToMany(mappedBy: 'groupement', targetEntity: Role::class, cascade: ['persist'])]
    private Collection $roles;

    /**
     * @var Collection<int, Campagne>
     */
    #[ORM\OneToMany(mappedBy: 'groupement', targetEntity: Campagne::class, cascade: ['persist'])]
    private Collection $campagnes;

    /**
     * @var Collection<int, Exercice>
     */
    #[ORM\OneToMany(mappedBy: 'groupement', targetEntity: Exercice::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $exercices;

    public function __construct()
    {
        $this->initializeDateTimes();
        $this->membres = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->campagnes = new ArrayCollection();
        $this->exercices = new ArrayCollection();
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

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

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->createdAt = $dateCreation instanceof \DateTimeImmutable ? \DateTime::createFromInterface($dateCreation) : new \DateTime($dateCreation->format('Y-m-d H:i:s'), $dateCreation->getTimezone());

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setGroupement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setGroupement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function getCampagnes(): Collection
    {
        return $this->campagnes;
    }

    public function addCampagne(Campagne $campagne): self
    {
        if (!$this->campagnes->contains($campagne)) {
            $this->campagnes->add($campagne);
            $campagne->setGroupement($this);
        }

        return $this;
    }

    public function addExercice(Exercice $exercice): self
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices->add($exercice);
            $exercice->setGroupement($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->removeElement($exercice)) {
            $exercice->setGroupement($this);
        }

        return $this;
    }
}
