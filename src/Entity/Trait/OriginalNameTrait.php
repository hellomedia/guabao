<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait OriginalNameTrait
{
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $originalName = null;

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getNameOriginal(): ?string
    {
        return $this->getOriginalName();
    }

    public function setNameOriginal(?string $nameOriginal): static
    {
        return $this->setOriginalName($nameOriginal);
    }
}
