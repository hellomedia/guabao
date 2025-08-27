<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Tag\FoodTag;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Enum\Level;
use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\ManyToOne(inversedBy: 'children')]
    private ?Food $parent = null;

    #[ORM\OneToMany(targetEntity: Food::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $loveLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $localLevel = null;

    #[ORM\Column(enumType: Level::class, nullable: true)]
    protected ?Level $healthyLevel = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: FoodTag::class)]
    private Collection $tags;

    #[ORM\ManyToOne]
    private ?Cuisine $cuisine = null;

    /**
     * @var Collection<int, cuisine>
     */
    #[ORM\ManyToMany(targetEntity: cuisine::class, inversedBy: 'food')]
    private Collection $cuisines;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'food')]
    private Collection $ingredients;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\ManyToMany(targetEntity: Media::class, mappedBy: 'food')]
    private Collection $medias;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->cuisines = new ArrayCollection();
        $this->medias = new ArrayCollection();
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

    public function getCuisine(): ?Cuisine
    {
        return $this->cuisine;
    }

    public function setCuisine(?Cuisine $cuisine): static
    {
        $this->cuisine = $cuisine;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

    public function getMeals(): array
    {
        $meals = [];

        foreach($this->medias as $media) {
            $meal = $media->getMeal();
            if ($meal instanceof Meal && !isset($seen[$meal->getId()])) {
                $seen[$meal->getId()] = true;
                $meals[] = $meal;
            }
        }

        return $meals;
    }


    public function isTopLevelFood(): bool
    {
        return $this->parent == null;
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function getParent(): ?Food
    {
        return $this->parent;
    }

    public function setParent(?Food $parent): self
    {
        $this->parent = $parent;

        if ($parent !== null) {
            $parent->addChild($this);
        }

        return $this;
    }

    public function addChild(Food $food): void
    {
        if (!$this->children->contains($food)) {
            $this->children[] = $food;
        }
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return Collection<int, cuisine>
     */
    public function getCuisines(): Collection
    {
        return $this->cuisines;
    }

    public function addCuisine(cuisine $cuisine): static
    {
        if (!$this->cuisines->contains($cuisine)) {
            $this->cuisines->add($cuisine);
        }

        return $this;
    }

    public function removeCuisine(cuisine $cuisine): static
    {
        $this->cuisines->removeElement($cuisine);

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
            $media->addFood($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->medias->removeElement($media)) {
            $media->removeFood($this);
        }

        return $this;
    }
}
