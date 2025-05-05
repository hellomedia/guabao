<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait LocalizedNameTrait
{
    #[Assert\NotBlank()]
    #[Assert\Length(max:100)]
    #[ORM\Column(length: 100)]
    private ?string $nameFr = null;

    #[Assert\NotBlank()]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    private ?string $nameEn = null;

    /**
     * Does not include locale flexibility 
     * but convenient for easyadmin fields, filters, etc.
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->nameFr ?? $this->nameEn,
            'en' => $this->nameEn ?? $this->nameFr,
            default => $this->nameFr ?? $this->nameEn,
        };
    }

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): static
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;

        return $this;
    }
}
