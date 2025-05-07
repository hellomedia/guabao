<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Tag\Tag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Repository\PictureRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[Vich\Uploadable]
class Picture implements EntityInterface
{
    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'picture_image', fileNameProperty: 'filePath')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    // used to trigger filePath update when unmapped imageFile is submitted
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $takenAt = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Food $Food = null;

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

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    private ?Place $place = null;

    /**
     * PlaceTags are useful when no place is attached to the picture.
     * Which might happen regularly (place is attached only for shops etc)
     * For consistency, we always add placeTags to the picture
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
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Picture #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable(); // Needed for Doctrine change tracking
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
        return $this->Food;
    }

    public function setFood(?Food $Food): static
    {
        $this->Food = $Food;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

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
        $this->highlightedTrip = $highlight ? $this->trip : null;

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
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
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
}
