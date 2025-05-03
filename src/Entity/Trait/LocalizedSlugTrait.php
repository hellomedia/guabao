<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait LocalizedSlugTrait
{
    #[ORM\Column(length: 100)]
    private ?string $slugFr = null;

    #[ORM\Column(length: 100)]
    private ?string $slugEn = null;

    public function getSlugFr(): ?string
    {
        return $this->slugFr;
    }

    public function setSlugFr(string $slugFr): static
    {
        $this->slugFr = $slugFr;

        return $this;
    }

    public function getSlugEn(): ?string
    {
        return $this->slugEn;
    }

    public function setSlugEn(string $slugEn): static
    {
        $this->slugEn = $slugEn;

        return $this;
    }

    public function getSlug(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->slugFr,
            'en' => $this->slugEn,
            default => $this->slugFr,
        };
    }
}
