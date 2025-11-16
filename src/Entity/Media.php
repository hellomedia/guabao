<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Enum\MediaType;
use App\Enum\VideoOrientation;
use Pack\Media\Entity\Interface\UploadedAssetEntityInterface;
use Pack\Media\Entity\Trait\ImageTrait;
use App\Repository\MediaRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Index('media_taken_at_idx', ['taken_at'])]
#[ORM\Index('media_show_in_trip_idx', ['show_in_trip'])]
#[ORM\Index('media_show_in_story_idx', ['show_in_story'])]
#[ORM\Index('media_show_in_food_idx', ['show_in_food'])]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media implements EntityInterface, UploadedAssetEntityInterface
{
    use ImageTrait;

    use LocalizedDescriptionTrait;

    /**
     * Override image trait properties and mappging
     * Because we want to make everything nullable
     * Since Media can also be a video
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalFilename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    /**
     * END Override image trait properties and mapping
     */

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $takenAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    private ?Place $place = null;

    /**
     * PlaceTags are useful when no place is attached to the media.
     * Which might happen regularly (place is attached only for shops etc)
     * For consistency, we always add placeTags to the media
     * It is also the safe link to country.
     * @var Collection<int, PlaceTag>
     */
    #[ORM\ManyToMany(targetEntity: PlaceTag::class)]
    private Collection $placeTags;

    /**
     * Same logic for latitude and longitude
     */
    #[ORM\Column(precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;

    /**
     * @var Collection<int, MediaTag>
     */
    #[ORM\ManyToMany(targetEntity: MediaTag::class)]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Meal $meal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isMeal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPano = null;

    // Un-mapped
    // Attn, do not call "isCover", it creates a confusion with trip->getCover() in twig when we use this:
    //  {% set media = attribute(entity.instance, 'cover') ?? entity.instance %}
    // because the attribute 'cover' triggers calls for getCover and isCover
    // In the twig snippet above, media is evaluated to isCover, which returns a boolean, not the expected Media entity
    private ?bool $isTripCover = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is360 = null;

    #[ORM\Column(enumType: MediaType::class)]
    private ?MediaType $type = null;

    #[ORM\Column(nullable: true)]
    private ?string $vimeoId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $takenAtHint = null;

    #[ORM\Column(enumType: VideoOrientation::class, nullable: true)]
    private ?VideoOrientation $videoOrientation = null;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Story $story = null;

    #[ORM\Column(nullable: true)]
    private ?bool $showInTrip = null;

    #[ORM\Column(nullable: true)]
    private ?bool $showInStory = null;

    #[ORM\Column(nullable: true)]
    private ?bool $showInFood = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\ManyToMany(targetEntity: Food::class, inversedBy: 'medias')]
    private Collection $food;

    /**
     * @var Collection<int, SiteHighlight>
     */
    #[ORM\ManyToMany(targetEntity: SiteHighlight::class, mappedBy: 'medias')]
    private Collection $siteHighlights;

    /* useful for highlighting single photo in story */
    #[ORM\Column(options: ['default' => false])]
    private bool $size2 = false;

    /* useful for highlighting single photo in story */
    #[ORM\Column(options: ['default' => false])]
    private bool $size3 = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimaryVideo = false;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
        $this->food = new ArrayCollection();
        $this->siteHighlights = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Media #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->takenAt;
    }

    public function setTakenAt(?\DateTimeImmutable $takenAt): static
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        if ($this->getMeal()) {
            $this->meal->setPlace($place);
        }

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        if ($this->isTripCover) {
            $trip->setCover($this);
        }

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return Collection<int, MediaTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(MediaTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(MediaTag $tag): static
    {
        $this->tags->removeElement($tag);

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
        }

        return $this;
    }

    public function removePlaceTag(PlaceTag $placeTag): static
    {
        $this->placeTags->removeElement($placeTag);

        return $this;
    }

    public function getCountry(): ?Country
    {
        return ($this->placeTags->first() ?: null)?->getCountry();
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): static
    {
        $this->meal = $meal;

        return $this;
    }

    public function isMeal(): ?bool
    {
        return $this->isMeal;
    }

    public function setIsMeal(?bool $isMeal): static
    {
        $this->isMeal = $isMeal;

        return $this;
    }

    public function isPano(): ?bool
    {
        return $this->isPano;
    }

    public function setIsPano(?bool $isPano): static
    {
        $this->isPano = $isPano;

        return $this;
    }

    public function isTripCover(): ?bool
    {
        return $this->trip?->getCover() === $this;
    }

    public function setIsTripCover(?bool $isTripCover): static
    {
        if ($this->trip instanceof Trip) {
            $this->trip->setCover($isTripCover ? $this : null);
        }

        return $this;
    }

    public function is360(): ?bool
    {
        return $this->is360;
    }

    public function setIs360(?bool $is360): static
    {
        $this->is360 = $is360;

        return $this;
    }

    public function getType(): ?MediaType
    {
        return $this->type;
    }

    public function setType(MediaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isImage(): bool
    {
        return $this->type == MediaType::IMAGE;
    }

    public function isVideo(): bool
    {
        return $this->type == MediaType::VIDEO;
    }

    public function getVimeoUrl(): ?string
    {
        return "https://vimeo.com/{$this->vimeoId}";
    }

    public function getVimeoId(): ?string
    {
        return $this->vimeoId;
    }

    public function setVimeoId(?string $vimeoId): static
    {
        $this->vimeoId = $vimeoId;

        return $this;
    }

    public function getTakenAtHint(): ?string
    {
        return $this->takenAtHint;
    }

    public function setTakenAtHint(?string $takenAtHint): static
    {
        $this->takenAtHint = $takenAtHint;

        return $this;
    }

    public function getVideoOrientation(): ?VideoOrientation
    {
        return $this->videoOrientation;
    }

    public function getOrientation(): ?VideoOrientation
    {
        return $this->videoOrientation;
    }

    public function setVideoOrientation(?VideoOrientation $videoOrientation): static
    {
        $this->videoOrientation = $videoOrientation;

        return $this;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): static
    {
        $this->story = $story;

        return $this;
    }

    public function getShowInTrip(): ?bool
    {
        return $this->showInTrip;
    }

    public function setShowInTrip(?bool $showInTrip): static
    {
        $this->showInTrip = $showInTrip;

        return $this;
    }

    public function getShowInStory(): ?bool
    {
        return $this->showInStory;
    }

    public function setShowInStory(?bool $showInStory): static
    {
        $this->showInStory = $showInStory;

        return $this;
    }

    public function getShowInFood(): ?bool
    {
        return $this->showInFood;
    }

    public function setShowInFood(?bool $showInFood): static
    {
        $this->showInFood = $showInFood;

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): static
    {
        if (!$this->food->contains($food)) {
            $this->food->add($food);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        $this->food->removeElement($food);

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
            $siteHighlight->addMedia($this);
        }

        return $this;
    }

    public function removeSiteHighlight(SiteHighlight $siteHighlight): static
    {
        if ($this->siteHighlights->removeElement($siteHighlight)) {
            $siteHighlight->removeMedia($this);
        }

        return $this;
    }

    public function isSize2(): bool
    {
        return $this->size2;
    }

    public function setSize2(bool $size2): static
    {
        $this->size2 = $size2;

        return $this;
    }

    public function isSize3(): bool
    {
        return $this->size3;
    }

    public function setSize3(bool $size3): static
    {
        $this->size3 = $size3;

        return $this;
    }

    public function isPrimaryVideo(): bool
    {
        return $this->isPrimaryVideo;
    }

    public function setIsPrimaryVideo(bool $isPrimaryVideo): static
    {
        $this->isPrimaryVideo = $isPrimaryVideo;

        return $this;
    }
}
