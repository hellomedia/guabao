<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'country')]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $code = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true)] // nullable for fixtures
    private ?Media $foodCover = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getFoodCover(): ?Media
    {
        return $this->foodCover;
    }

    public function setFoodCover(Media $foodCover): static
    {
        $this->foodCover = $foodCover;

        return $this;
    }
}
