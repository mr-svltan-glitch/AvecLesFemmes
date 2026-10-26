<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    protected string $nomComplet;

    #[ORM\Column(length: 30, nullable: true)]
    protected ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $adresse = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    protected string $login;

    #[ORM\Column(length: 255)]
    protected string $password;

    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Groupement $groupement = null;

    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Role $role = null;

    /**
     * @var Collection<int, ModeCotisation>
     */
    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: ModeCotisation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $modeCotisations;

    public function __construct()
    {
        $this->modeCotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

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

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(?Groupement $groupement): self
    {
        $this->groupement = $groupement;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

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
