<?php

namespace App\Entity;

use App\Entity\FoodItem\BakeryItem;
use App\Entity\FoodItem\Breakfast;
use App\Entity\FoodItem\Dessert;
use App\Entity\FoodItem\Dish;
use App\Entity\FoodItem\Drink;
use App\Entity\FoodItem\Fruit;
use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Tag\Tag;
use App\Repository\PictureRepository;
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

    // 2 things:
    //   - Keep association with Food Item link for querying across all food items
    //   - This is the inversed association that links fooditems and child types (dish, etc.) to their picture
    // BUT it's not the association that gets set in picture form in easy admin (see $dish, etc. below)
    // ===> we synchronise fooditem property in the settter for all types (setDish(), etc.)
    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: true)]
    private ?FoodItem $foodItem = null;

    // Add associations to specific types for easyadmin
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dish $dish = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Breakfast $breakfast = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Drink $drink = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?BakeryItem $bakeryItem = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dessert $dessert = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fruit $fruit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $caption = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    private ?Trip $trip = null;

    #[ORM\Column(nullable: true)]
    private ?bool $highlight = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cover = null;

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

    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->takenAt;
    }

    public function setTakenAt(?\DateTimeImmutable $takenAt): static
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    public function getFoodItem(): ?FoodItem
    {
        return $this->foodItem;
    }

    public function setFoodItem(?FoodItem $foodItem): static
    {
        $this->foodItem = $foodItem;

        return $this;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): static
    {
        $this->dish = $dish;

        if ($dish != null) {
            $this->foodItem = $dish;
        }

        return $this;
    }

    public function getBreakfast(): ?Breakfast
    {
        return $this->breakfast;
    }

    public function setBreakfast(?Breakfast $breakfast): static
    {
        $this->breakfast = $breakfast;

        if ($breakfast != null) {
            $this->foodItem = $breakfast;
        }

        return $this;
    }

    public function getDessert(): ?Dessert
    {
        return $this->dessert;
    }

    public function setDessert(?Dessert $dessert): static
    {
        $this->dessert = $dessert;

        if ($dessert != null) {
            $this->foodItem = $dessert;
        }

        return $this;
    }

    public function getFruit(): ?Fruit
    {
        return $this->fruit;
    }

    public function setFruit(?Fruit $fruit): static
    {
        $this->fruit = $fruit;

        if ($fruit != null) {
            $this->foodItem = $fruit;
        }

        return $this;
    }

    public function getBakeryItem(): ?BakeryItem
    {
        return $this->bakeryItem;
    }

    public function setBakeryItem(?BakeryItem $bakeryItem): static
    {
        $this->bakeryItem = $bakeryItem;

        if ($bakeryItem != null) {
            $this->foodItem = $bakeryItem;
        }

        return $this;
    }

    public function getDrink(): ?Drink
    {
        return $this->drink;
    }

    public function setDrink(?Drink $drink): static
    {
        $this->drink = $drink;

        if ($drink != null) {
            $this->foodItem = $drink;
        }

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

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;

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

        return $this;
    }

    public function isCover(): ?bool
    {
        return $this->cover;
    }

    public function setCover(?bool $cover): static
    {
        $this->cover = $cover;

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
}
