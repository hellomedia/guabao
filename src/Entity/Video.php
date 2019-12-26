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
     * @ORM\OneToMany(targetEntity="App\Entity\VideoTag", mappedBy="video", orphanRemoval=true, fetch="EAGER")
     */
    private $videoTags;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Person", mappedBy="videos", cascade="persist", fetch="EAGER")
     */
    private $people;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="videos", fetch="EAGER")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Duration", inversedBy="videos", fetch="EAGER")
     */
    private $duration;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->videoTags = new ArrayCollection();
        $this->people = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
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

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->videoTags->map(function($videoTag) {
            return $videoTag->getTag();
        });
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->addVideo($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
            $person->removeVideo($this);
        }

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getDuration(): ?Duration
    {
        return $this->duration;
    }

    public function setDuration(?Duration $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
