<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Override trait to make nullable (existing data)
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $slugFr = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $slugEn = null;

    #[Assert\Length(max: 5000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textBelowGalleryFr = null;

    #[Assert\Length(max: 5000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textBelowGalleryEn = null;

    #[ORM\ManyToOne(inversedBy: 'stories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OrderBy(['takenAt' => 'ASC'])]
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'story', fetch: 'EAGER')]
    private Collection $medias;

    /**
     * @var Collection<int, MediaTag>
     */
    #[ORM\ManyToMany(targetEntity: MediaTag::class)]
    private Collection $tags;

    /**
     * @var Collection<int, PlaceTag>
     */
    #[ORM\ManyToMany(targetEntity: PlaceTag::class)]
    private Collection $placeTags;

    #[ORM\Column(nullable: true)]
    private ?int $displayOrder = null;

    /**
     * @var Collection<int, SiteHighlight>
     */
    #[ORM\ManyToMany(targetEntity: SiteHighlight::class, mappedBy: 'stories')]
    private Collection $siteHighlights;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $showTitle = true;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $show = true;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->placeTags = new ArrayCollection();
        $this->siteHighlights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextBelowGallery(?string $locale = null): ?string
    {
        return match ($locale) {
            'fr' => $this->textBelowGalleryFr,
            'en' => $this->textBelowGalleryEn,
            default => $this->textBelowGalleryEn ?? $this->textBelowGalleryFr,
        };
    }

    public function getTextBelowGalleryFr(): ?string
    {
        return $this->textBelowGalleryFr;
    }

    public function setTextBelowGalleryFr(?string $textBelowGalleryFr): static
    {
        $this->textBelowGalleryFr = $textBelowGalleryFr;

        return $this;
    }

    public function getTextBelowGalleryEn(): ?string
    {
        return $this->textBelowGalleryEn;
    }

    public function setTextBelowGalleryEn(?string $textBelowGalleryEn): static
    {
        $this->textBelowGalleryEn = $textBelowGalleryEn;

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

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayableMedias(): Collection
    {
        return $this->medias->filter(function($media) {
            return $media->getShowInStory();
        });
    }

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayableImages(): Collection
    {
        return $this->medias->filter(function(Media $media) {
            return $media->getShowInStory() && $media->isImage() && !$media->is360();
        });
    }

    /**
     * Auto cover = first image
     */
    public function getCover(): ?Media
    {
        $images = $this->medias->filter(function(Media $media) {
            return $media->isImage() && !$media->is360();
        });

        if ($images->isEmpty()) {
            return null;
        }

        return $images->first();
    }

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayable360s(): Collection
    {
        return $this->medias->filter(function(Media $media) {
            return $media->getShowInStory() && $media->is360();
        });
    }

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayableVideos(): Collection
    {
        return $this->medias->filter(function($media) {
            return $media->getShowInStory() && $media->isVideo();
        });
    }

    public function getDisplayablePrimaryVideo(): ?Media
    {
        $video = $this->medias->filter(function ($media) {
            return $media->getShowInStory() && $media->isVideo() && $media->isPrimaryVideo();
        })->first();

        // if no primary video, $video = false

        return $video ? $video : null;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getDisplayableSecondaryVideos(): Collection
    {
        return $this->medias->filter(function($media) {
            return $media->getShowInStory() && $media->isVideo() && !$media->isPrimaryVideo();
        });
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setStory($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getStory() === $this) {
                $media->setStory(null);
            }
        }

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
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
            $siteHighlight->addStory($this);
        }

        return $this;
    }

    public function removeSiteHighlight(SiteHighlight $siteHighlight): static
    {
        if ($this->siteHighlights->removeElement($siteHighlight)) {
            $siteHighlight->removeStory($this);
        }

        return $this;
    }

    public function showTitle(): ?bool
    {
        return $this->showTitle;
    }

    public function isShowTitle(): ?bool
    {
        return $this->showTitle;
    }

    public function setShowTitle(bool $showTitle): static
    {
        $this->showTitle = $showTitle;

        return $this;
    }

    public function show(): ?bool
    {
        return $this->show;
    }

    public function isShow(): ?bool
    {
        return $this->show;
    }

    public function setShow(bool $show): static
    {
        $this->show = $show;

        return $this;
    }

    public function display(): bool
    {
        if (!$this->show) {
            return false;
        }

        if ($this->getDisplayableImages()->count() > 0) {
            return true;
        }

        if ($this->getDisplayableVideos()->count() > 0) {
            return true;
        }

        if (!empty($this->getDescription())) {
            return true;
        }

        return false;
    }
}
