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
use App\Entity\Trait\ShortNameTrait;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Index('trip_started_at_idx', ['started_at'])]
#[ORM\Index('trip_duration_idx', ['duration'])]
#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip implements LocalizedNameInterface, LocalizedSlugInterface, HasPeriodInterface, EntityInterface
{
    use LocalizedNameTrait;

    use ShortNameTrait;

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
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')] // nullable for fixtures
    private ?Media $cover = null;

    /**
     * @var Collection<int, TripTag>
     */
    #[ORM\ManyToMany(targetEntity: TripTag::class)]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'children')]
    private ?Trip $parent = null;

    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'parent')]
    #[ORM\OrderBy(["startedAt" => "ASC"])]
    private Collection $children;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(5)]
    #[ORM\Column(nullable: true)]
    private ?int $adventureRating = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(5)]
    #[ORM\Column(nullable: true)]
    private ?int $durationRating = null;

    // Duration in days (used for sorting by duration)
    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    /**
     * @var Collection<int, Story>
     */
    #[ORM\OrderBy(['displayOrder' => 'ASC'])]
    #[ORM\OneToMany(targetEntity: Story::class, mappedBy: 'trip')]
    private Collection $stories;

    /**
     * @var Collection<int, SiteHighlight>
     */
    #[ORM\ManyToMany(targetEntity: SiteHighlight::class, mappedBy: 'trips')]
    private Collection $siteHighlights;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->stories = new ArrayCollection();
        $this->siteHighlights = new ArrayCollection();
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

    public function getCover(): ?Media
    {
        return $this->cover;
    }

    public function setCover(?Media $cover): static
    { 
        $this->cover = $cover;

        return $this;
    }

    public function isTripLeg(): bool
    {
        return $this->hasParent();
    }

    public function hasParent(): bool
    {
        return $this->parent != null;
    }

    public function isTopLevelTrip(): bool
    {
        return $this->parent == null;
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function getParent(): ?Trip
    {
        return $this->parent;
    }

    public function setParent(?Trip $parent): self
    {
        $this->parent = $parent;

        if ($parent !== null) {
            $parent->addChild($this);
        }

        return $this;
    }

    public function addChild(Trip $trip): void
    {
        if (!$this->children->contains($trip)) {
            $this->children[] = $trip;
        }
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection<int, TripTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(TripTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(TripTag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getAdventureRating(): ?int
    {
        return $this->adventureRating;
    }

    public function setAdventureRating(?int $adventureRating): static
    {
        $this->adventureRating = $adventureRating;

        return $this;
    }

    public function getDurationRating(): ?int
    {
        return $this->durationRating;
    }

    public function setDurationRating(?int $durationRating): static
    {
        $this->durationRating = $durationRating;

        return $this;
    }

    public function getShortNameWithFallback(?string $locale = null): string
    {
        return $this->getShortName($locale) ?? $this->getShortNameFallback($locale);
    }

    public function getShortNameFallback(?string $locale = null): string
    {
        return $this->getName($locale) . ($this->isTopLevelTrip() ? ' ' . $this->getPeriod() : '');
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Story>
     */
    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function addStory(Story $story): static
    {
        if (!$this->stories->contains($story)) {
            $this->stories->add($story);
            $story->setTrip($this);
        }

        return $this;
    }

    public function removeStory(Story $story): static
    {
        if ($this->stories->removeElement($story)) {
            // set the owning side to null (unless already changed)
            if ($story->getTrip() === $this) {
                $story->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SiteHighlight>
     */
    public function getSiteHighlights(): Collection
    {
        return $this->siteHighlights;
    }

    public function addSiteHighlight(SiteHighlight $siteHighlight): static
    {
        if (!$this->siteHighlights->contains($siteHighlight)) {
            $this->siteHighlights->add($siteHighlight);
            $siteHighlight->addTrip($this);
        }

        return $this;
    }

    public function removeSiteHighlight(SiteHighlight $siteHighlight): static
    {
        if ($this->siteHighlights->removeElement($siteHighlight)) {
            $siteHighlight->removeTrip($this);
        }

        return $this;
    }
}
