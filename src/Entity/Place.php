<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place implements EntityInterface
{
    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]   
    private int $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null; // e.g. "123 Rue de Rivoli, 75001 Paris"

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $googlePlaceId = null;

    #[ORM\Column(precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;

    /**
     * @var Collection<int, PlaceTag>
     */
    #[ORM\ManyToMany(targetEntity: PlaceTag::class)]
    private Collection $placeTags;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'place')]
    private Collection $medias;

    /**
     * @var Collection<int, Meal>
     */
    #[ORM\OneToMany(targetEntity: Meal::class, mappedBy: 'place')]
    private Collection $meals;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->meals = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlaceId;
    }

    public function setGooglePlaceId(?string $googlePlaceId): void
    {
        $this->googlePlaceId = $googlePlaceId;
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
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setPlace($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getPlace() === $this) {
                $media->setPlace(null);
            }
        }

        return $this;
    }

    public function getGoogleMapsUrl(): ?string
    {
        return $this->googlePlaceId
            ? 'https://www.google.com/maps/place/?q=place_id=' . $this->googlePlaceId
            : null;
    }

    public function getGoogleMapsLink(): ?string
    {
        return $this->googlePlaceId
            ? sprintf('<a href="https://www.google.com/maps/place/?q=place_id=%s" target="_blank">%s</a>', $this->googlePlaceId, htmlspecialchars($this->name ?? 'Google Maps'))
            : null;
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

    /**
     * @return Collection<int, Meal>
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(Meal $meal): static
    {
        if (!$this->meals->contains($meal)) {
            $this->meals->add($meal);
            $meal->setPlace($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): static
    {
        if ($this->meals->removeElement($meal)) {
            // set the owning side to null (unless already changed)
            if ($meal->getPlace() === $this) {
                $meal->setPlace(null);
            }
        }

        return $this;
    }
}
