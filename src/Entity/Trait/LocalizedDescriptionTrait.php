<?php

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait LocalizedDescriptionTrait
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    public function getDescription(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->descriptionFr ?? $this->descriptionEn,
            'en' => $this->descriptionEn ?? $this->descriptionFr,
            default => $this->descriptionFr ?? $this->descriptionEn,
        };
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(string $descriptionFr): static
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn): static
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }
}
