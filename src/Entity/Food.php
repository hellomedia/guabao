<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\FoodTag;
use App\Entity\FoodType;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Enum\Level;
use App\Enum\Month;
use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $loveLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $localLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $healthyLevel = null;

    #[ORM\Column(enumType: Month::class, nullable: true)]
    private ?Month $seasonStart = null;

    #[ORM\Column(enumType: Month::class, nullable: true)]
    private ?Month $seasonEnd = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'food')]
    private Collection $pictures;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: FoodTag::class)]
    private Collection $tags;

    #[ORM\ManyToOne]
    private ?FoodType $type = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoveLevel(): ?Level
    {
        return $this->loveLevel;
    }

    public function setLoveLevel(Level $loveLevel): static
    {
        $this->loveLevel = $loveLevel;

        return $this;
    }

    public function getLocalLevel(): ?Level
    {
        return $this->localLevel;
    }

    public function setLocalLevel(Level $localLevel): static
    {
        $this->localLevel = $localLevel;

        return $this;
    }

    public function getHealthyLevel(): ?Level
    {
        return $this->healthyLevel;
    }

    public function setHealthyLevel(Level $healthyLevel): static
    {
        $this->healthyLevel = $healthyLevel;

        return $this;
    }

    public function getSeasonStart(): ?Month
    {
        return $this->seasonStart;
    }

    public function setSeasonStart(?Month $seasonStart): static
    {
        $this->seasonStart = $seasonStart;

        return $this;
    }

    public function getSeasonEnd(): ?Month
    {
        return $this->seasonEnd;
    }

    public function setSeasonEnd(?Month $seasonEnd): static
    {
        $this->seasonEnd = $seasonEnd;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setFood($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getFood() === $this) {
                $picture->setFood(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FoodTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(FoodTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(FoodTag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getType(): ?FoodType
    {
        return $this->type;
    }

    public function setType(?FoodType $type): static
    {
        $this->type = $type;

        return $this;
    }
}