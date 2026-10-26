<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends Membre
{
    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Seance::class)]
    private Collection $seances;

    public function __construct()
    {
        parent::__construct();
        $this->seances = new ArrayCollection();
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
        $seance->setAdmin($this);

        return $this;
    }
}
