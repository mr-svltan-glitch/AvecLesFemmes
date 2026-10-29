<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DateTimeTrait
{
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    protected function initializeDateTimes(): void
    {
        if (!isset($this->createdAt) || $this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }

        if (!isset($this->updatedAt) || $this->updatedAt === null) {
            $this->updatedAt = new \DateTime();
        }
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    private function initDat(): void
    {
        if (!isset($this->createdAt) || $this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt instanceof \DateTimeImmutable ? \DateTime::createFromInterface($createdAt) : $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt instanceof \DateTimeImmutable ? \DateTime::createFromInterface($updatedAt) : $updatedAt;

        return $this;
    }
}
