<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Tag\MediaTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story implements LocalizedNameInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    #[ORM\Column(nullable: true)]
    private ?int $displayOrder = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->medias->filter(function($media) {
            return $media->getShowInStory() && $media->isImage();
        });
    }

    public function getDisplayableVideo(): Media|false
    {
        $collection = $this->medias->filter(function($media) {
            return $media->getShowInStory() && $media->isVideo();
        });

        return $collection->first();
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
}
