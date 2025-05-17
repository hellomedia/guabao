<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Enum\MediaType;
use App\Pack\Media\Entity\Interface\UploadedAssetEntityInterface;
use App\Pack\Media\Entity\Trait\ImageTrait;
use App\Repository\MediaRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media implements EntityInterface, UploadedAssetEntityInterface
{
    use ImageTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $takenAt = null;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Food $food = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Trip $trip = null;

    #[ORM\Column(nullable: true)]
    private ?bool $highlight = null;
    
    // highlightedTrip below is redundant with trip above
    // but it allows doctrine to have a trip#highlights association
    // which is convenient for queries and easyadmin
    // highligthedTrip is handled inside setHighlight
    #[ORM\ManyToOne(inversedBy: 'highlights')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Trip $highlightedTrip = null;

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoUrl = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
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

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): static
    {
        $this->food = $food;

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

        // auto set highlightedTrip convenience property
        if ($this->highlight) {
            $this->highlightedTrip = $trip;
        }

        if ($this->isTripCover) {
            $trip->setCover($this);
        }

        return $this;
    }

    public function isHighlight(): ?bool
    {
        return $this->highlight;
    }

    public function setHighlight(?bool $highlight): static
    {
        $this->highlight = $highlight;
        
        // auto set highlightedTrip convenience property
        $this->highlightedTrip = ($highlight ? $this->trip : null);

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
        return $this->placeTags->first()?->getCountry();
    }

    public function getHighlightedTrip(): ?Trip
    {
        return $this->highlightedTrip;
    }

    public function setHighlightedTrip(?Trip $highlightedTrip): static
    {
        $this->highlightedTrip = $highlightedTrip;

        return $this;
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

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }
}
