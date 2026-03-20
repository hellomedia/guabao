<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Enum\MealType;
use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal implements EntityInterface
{
    use LocalizedDescriptionTrait;

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

    /**
     * @var Collection<int, SiteHighlight>
     */
    #[ORM\ManyToMany(targetEntity: SiteHighlight::class, mappedBy: 'meals')]
    private Collection $siteHighlights;

    #[ORM\Column(nullable: true)]
    private ?bool $favourite = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
        $this->siteHighlights = new ArrayCollection();
    }

    /**
     * Meals are created automatically from a food media 
     * in MediaAutoFillHelper::setMeal when isMeal flag is set to true
     */
    public function __toString()
    {
        return  $this->getNameAndTime();
    }

    public function getNameAndTime(): string
    {
        return $this->getName() . ' ' . $this->enjoyedAt->format('d M Y H\h');
    }

    public function getName(): string
    {
        return ($this->type?->toEnglish() ?: 'Meal') . ' @ ' . ($this->place?->getName() ?: $this->placeTags?->first());
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

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayableMedias(): Collection
    {
        return $this->medias->filter(function ($media) {
            return $media->getShowInFood();
        });
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
            $siteHighlight->addMeal($this);
        }

        return $this;
    }

    public function removeSiteHighlight(SiteHighlight $siteHighlight): static
    {
        if ($this->siteHighlights->removeElement($siteHighlight)) {
            $siteHighlight->removeMeal($this);
        }

        return $this;
    }

    public function isFavourite(): ?bool
    {
        return $this->favourite;
    }

    public function setFavourite(?bool $favourite): static
    {
        $this->favourite = $favourite;

        return $this;
    }
}
