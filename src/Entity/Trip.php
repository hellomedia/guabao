<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\KeyTrait;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedDescriptionTrait;

    use KeyTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $endedAt = null;

    /**
     * Let's link to countries directly from trip
     * This is duplication since pics are linked to trip and placetags, which are linked to countries
     * But it seems like it also belongs to the trip itself, and is not much work
     * @var Collection<int, Country>
     */
    #[ORM\ManyToMany(targetEntity: Country::class)]
    private Collection $countries;

    /**
     * Unmapped. Convenience property.
     * @var Collection<int, PlaceTag>
     */
    private Collection $placeTags;

    /**
     * Unmapped. Convenience property
     */
    private ?Picture $cover = null;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeImmutable $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): static
    {
        if (!$this->countries->contains($country)) {
            $this->countries->add($country);
        }

        return $this;
    }

    public function removeCountry(Country $country): static
    {
        $this->countries->removeElement($country);

        return $this;
    }

    /**
     * @return Collection<int, PlaceTag>
     */
    public function getPlaceTags(): Collection
    {
        return $this->placeTags;
    }

    /**
     * @var Collection<int, PlaceTag>
     */
    public function setPlaceTags(Collection $placeTags): static
    {
        $this->placeTags = $placeTags;

        return $this;
    }

    public function setCover(?Picture $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getCover(): ?Picture
    {
        return $this->cover;
    }

    public function getPeriod(): string
    {
        if ($this->startedAt->format('m-Y') == $this->endedAt->format('m-Y')) {
            return $this->startedAt->format('M Y');
        }

        return $this->startedAt->format('M Y') . ' - ' . $this->endedAt->format('M Y');
    }
}
