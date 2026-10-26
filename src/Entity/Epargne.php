<?php

namespace App\Entity;

use App\Repository\EpargneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EpargneRepository::class)]
class Epargne extends Campagne
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $but;

    #[ORM\Column(type: 'float')]
    private float $benefice;

    public function getBut(): string
    {
        return $this->but;
    }

    public function setBut(string $but): self
    {
        $this->but = $but;

        return $this;
    }

    public function getBenefice(): float
    {
        return $this->benefice;
    }

    public function setBenefice(float $benefice): self
    {
        $this->benefice = $benefice;

        return $this;
    }
}
