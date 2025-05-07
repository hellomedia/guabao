<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\HasPeriodInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\TripTag;
use App\Entity\Trait\HasPeriodTrait;
use App\Entity\Trait\KeyTrait;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedHeadlineTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip implements LocalizedNameInterface, LocalizedSlugInterface, HasPeriodInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedHeadlineTrait;

    use LocalizedDescriptionTrait;

    use KeyTrait;

    use HasPeriodTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Let's link to countries directly from trip
     * This is duplication since pics are linked to trip and placetags, which are linked to countries
     * But it seems like it also belongs to the trip itself, and is not much work
     * @var Collection<int, Country>
     */
    #[ORM\ManyToMany(targetEntity: Country::class)]
    private Collection $countries;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true)] // nullable for fixtures
    private ?Picture $cover = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'highlightedTrip')]
    private Collection $highlights;

    /**
     * @var Collection<int, TripTag>
     */
    #[ORM\ManyToMany(targetEntity: TripTag::class)]
    private Collection $tripTags;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->highlights = new ArrayCollection();
        $this->tripTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Picture>
     */
    public function getHighlights(): Collection
    {
        return $this->highlights;
    }

    public function addHighlight(Picture $highlight): static
    {
        if (!$this->highlights->contains($highlight)) {
            $this->highlights->add($highlight);
            $highlight->setHighlightedTrip($this);
        }

        return $this;
    }

    public function removeHighlight(Picture $highlight): static
    {
        if ($this->highlights->removeElement($highlight)) {
            // set the owning side to null (unless already changed)
            if ($highlight->getHighlightedTrip() === $this) {
                $highlight->setHighlightedTrip(null);
            }
        }

        return $this;
    }

    public function getCover(): ?Picture
    {
        return $this->cover;
    }

    public function setCover(Picture $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection<int, TripTag>
     */
    public function getTripTags(): Collection
    {
        return $this->tripTags;
    }

    public function addTripTag(TripTag $tripTag): static
    {
        if (!$this->tripTags->contains($tripTag)) {
            $this->tripTags->add($tripTag);
        }

        return $this;
    }

    public function removeTripTag(TripTag $tripTag): static
    {
        $this->tripTags->removeElement($tripTag);

        return $this;
    }
}
