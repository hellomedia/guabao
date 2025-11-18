<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, PlaceTag>
     */
    #[ORM\OneToMany(targetEntity: PlaceTag::class, mappedBy: 'country')]
    private Collection $placeTags;

    public function __construct()
    {
        $this->placeTags = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, PlaceTag>
     */
    public function getPlaceTags(): Collection
    {
        return $this->placeTags;
    }

    public function addPlaceTag(PlaceTag $placeTag): static
    {
        if (!$this->placeTags->contains($placeTag)) {
            $this->placeTags->add($placeTag);
            $placeTag->setCountry($this);
        }

        return $this;
    }

    public function removePlaceTag(PlaceTag $placeTag): static
    {
        if ($this->placeTags->removeElement($placeTag)) {
            // set the owning side to null (unless already changed)
            if ($placeTag->getCountry() === $this) {
                $placeTag->setCountry(null);
            }
        }

        return $this;
    }
}
