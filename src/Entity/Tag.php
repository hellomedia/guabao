<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VideoTag", mappedBy="tag", orphanRemoval=true)
     */
    private $videoTags;

    public function __construct()
    {
        $this->videoTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $videoTag->setTag($this);
        }

        return $this;
    }

    public function removeVideoTag(VideoTag $videoTag): self
    {
        if ($this->videoTags->contains($videoTag)) {
            $this->videoTags->removeElement($videoTag);
            // set the owning side to null (unless already changed)
            if ($videoTag->getTag() === $this) {
                $videoTag->setTag(null);
            }
        }

        return $this;
    }
}