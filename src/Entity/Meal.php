<?php

namespace App\Entity;

use App\Entity\Tag\PlaceTag;
use App\Enum\MealType;
use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $enjoyedAt = null;

    #[ORM\Column(nullable: true, enumType: MealType::class)]
    private ?MealType $type = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'meal')]
    private Collection $medias;

    #[ORM\ManyToOne(inversedBy: 'meals')]
    private ?Place $place = null;

    /**
     * @var Collection<int, PlaceTag>
     */
    #[ORM\ManyToMany(targetEntity: PlaceTag::class)]
    private Collection $placeTags;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Meal ' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnjoyedAt(): ?\DateTimeImmutable
    {
        return $this->enjoyedAt;
    }

    public function setEnjoyedAt(?\DateTimeImmutable $enjoyedAt): static
    {
        $this->enjoyedAt = $enjoyedAt;

        return $this;
    }

    public function getType(): ?MealType
    {
        return $this->type;
    }

    public function setType(?MealType $type): static
    {
        $this->type = $type;

        return $this;
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
            $media->setMeal($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getMeal() === $this) {
                $media->setMeal(null);
            }
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

    public function getTrip(): ?Trip
    {
        return $this->medias?->first->getTrip();
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
}
