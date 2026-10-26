<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $nom;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupement $groupement = null;

    /**
     * @var Collection<int, Membre>
     */
    #[ORM\OneToMany(mappedBy: 'role', targetEntity: Membre::class)]
    private Collection $membres;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
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

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(Groupement $groupement): self
    {
        if ($this->groupement === $groupement) {
            return $this;
        }

        $this->groupement?->getRoles()->removeElement($this);
        $this->groupement = $groupement;
        if (!$groupement->getRoles()->contains($this)) {
            $groupement->getRoles()->add($this);
        }

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
        $membre->setRole($this);

        return $this;
    }
}
