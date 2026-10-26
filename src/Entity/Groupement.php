<?php

namespace App\Entity;

use App\Repository\GroupementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupementRepository::class)]
class Groupement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $nom;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreation;

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

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
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

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

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
        $membre->setGroupement($this);

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
        $role->setGroupement($this);

        return $this;
    }
}
