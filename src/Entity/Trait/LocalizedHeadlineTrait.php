<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait LocalizedHeadlineTrait
{
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headlineFr = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headlineEn = null;

    public function getHeadline(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->headlineFr,
            'en' => $this->headlineEn,
            default => $this->headlineEn ?? $this->headlineFr,
        };
    }

    public function getHeadlineFr(): ?string
    {
        return $this->headlineFr;
    }

    public function setHeadlineFr(string $headlineFr): static
    {
        $this->headlineFr = $headlineFr;

        return $this;
    }

    public function getHeadlineEn(): ?string
    {
        return $this->headlineEn;
    }

    public function setHeadlineEn(string $headlineEn): static
    {
        $this->headlineEn = $headlineEn;

        return $this;
    }
}
