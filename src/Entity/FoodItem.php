<?php

namespace App\Entity;

use App\Entity\FoodItem\BakeryItem;
use App\Entity\FoodItem\Breakfast;
use App\Entity\FoodItem\Dessert;
use App\Entity\FoodItem\Dish;
use App\Entity\FoodItem\Drink;
use App\Entity\FoodItem\Fruit;
use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\FoodTag;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Enum\Level;
use App\Enum\Month;
use App\Repository\FoodItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodItemRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'dish' => Dish::class,
    'breakfast' => Breakfast::class,
    'dessert' => Dessert::class,
    'drink' => Drink::class,
    'fruit' => Fruit::class,
    'bakery_item' => BakeryItem::class,
])]
abstract class FoodItem implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(enumType: Level::class, nullable: false)]
    protected ?Level $loveLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: false)]
    protected ?Level $localLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: false)]
    protected ?Level $healthyLevel = null;

    #[ORM\Column(nullable: true, enumType: Month::class)]
    private ?Month $seasonStart = null;

    #[ORM\Column(nullable: true, enumType: Month::class)]
    private ?Month $seasonEnd = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'foodItem')]
    private Collection $pictures;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: FoodTag::class)]
    private Collection $foodTags;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->foodTags = new ArrayCollection();
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
            $picture->setFoodItem($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getFoodItem() === $this) {
                $picture->setFoodItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FoodTag>
     */
    public function getFoodTags(): Collection
    {
        return $this->foodTags;
    }

    public function addFoodTag(FoodTag $foodTag): static
    {
        if (!$this->foodTags->contains($foodTag)) {
            $this->foodTags->add($foodTag);
        }

        return $this;
    }

    public function removeFoodTag(FoodTag $foodTag): static
    {
        $this->foodTags->removeElement($foodTag);

        return $this;
    }
}