<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ShortNameTrait
{
    #[Assert\Length(max:35)]
    #[ORM\Column(length: 35, nullable: true)]
    private ?string $shortNameFr = null;

    #[Assert\Length(max: 35)]
    #[ORM\Column(length: 35, nullable: true)]
    private ?string $shortNameEn = null;

    public function getShortName(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->shortNameFr ?? $this->shortNameEn,
            'en' => $this->shortNameEn ?? $this->shortNameFr,
            default => $this->shortNameFr ?? $this->shortNameEn,
        };
    }

    public function getShortNameFr(): ?string
    {
        return $this->shortNameFr;
    }

    public function setShortNameFr(string $shortNameFr): static
    {
        $this->shortNameFr = $shortNameFr;

        return $this;
    }

    public function getShortNameEn(): ?string
    {
        return $this->shortNameEn;
    }

    public function setShortNameEn(string $shortNameEn): static
    {
        $this->shortNameEn = $shortNameEn;

        return $this;
    }
}
