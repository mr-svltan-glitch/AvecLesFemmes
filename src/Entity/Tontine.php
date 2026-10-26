<?php

namespace App\Entity;

use App\Repository\TontineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TontineRepository::class)]
class Tontine extends Campagne
{
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $numero;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $client;

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }
}
