<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['membre' => Membre::class, 'admin' => Admin::class])]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    protected string $nomComplet;

    #[ORM\Column(length: 30)]
    protected string $telephone;

    #[ORM\Column(length: 255)]
    protected string $adresse;

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
    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: ModeCotisation::class, cascade: ['persist'])]
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

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
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

    public function setGroupement(Groupement $groupement): self
    {
        if ($this->groupement === $groupement) {
            return $this;
        }

        $this->groupement?->getMembres()->removeElement($this);
        $this->groupement = $groupement;
        if (!$groupement->getMembres()->contains($this)) {
            $groupement->getMembres()->add($this);
        }

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        if ($this->role === $role) {
            return $this;
        }

        $this->role?->getMembres()->removeElement($this);
        $this->role = $role;
        if (!$role->getMembres()->contains($this)) {
            $role->getMembres()->add($this);
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

    public function addModeCotisation(ModeCotisation $modeCotisation): self
    {
        $modeCotisation->setMembre($this);

        return $this;
    }
}
