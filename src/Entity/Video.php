<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VideoTag", mappedBy="video", orphanRemoval=true)
     */
    private $videoTags;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->videoTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|VideoTag[]
     */
    public function getVideoTags(): Collection
    {
        return $this->videoTags;
    }

    public function addVideoTag(VideoTag $videoTag): self
    {
        if (!$this->videoTags->contains($videoTag)) {
            $this->videoTags[] = $videoTag;
            $videoTag->setVideo($this);
        }

        return $this;
    }

    public function removeVideoTag(VideoTag $videoTag): self
    {
        if ($this->videoTags->contains($videoTag)) {
            $this->videoTags->removeElement($videoTag);
            // set the owning side to null (unless already changed)
            if ($videoTag->getVideo() === $this) {
                $videoTag->setVideo(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
