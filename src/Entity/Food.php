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

    #[ORM\ManyToOne]
    private ?Media $cover = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: FoodTag::class, inversedBy: 'food')]
    private Collection $tags;

    /**
     * @var Collection<int, cuisine>
     */
    #[ORM\ManyToMany(targetEntity: Cuisine::class, inversedBy: 'food')]
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

    public function getCover(): ?Media
    {
        return $this->cover;
    }

    public function setCover(?Media $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Grouping logic for images in the food details page
     * 
     * Images are group by date and by meal, with meal having precedence
     * 
     * @return array<int, array{
     *     type: 'meal'|'date',
     *     meal: ?Meal,
     *     date: ?\DateTimeImmutable,
     *     medias: Media[],
     *     place: ?Place,
     *     placeTags: PlaceTags[],
     *     sort: int
     * }>
     */
    public function getMediaGroups(): array
    {
        $groups = [];

        foreach ($this->getMedias() as $media) {
            $meal = $media->getMeal();

            if ($meal !== null) {
                // Group by meal (takes precedence)
                $key = 'meal_' . $meal->getId();
                // Adjust these according to your Meal entity
                $groupDate = $meal->getEnjoyedAt();
                $type  = 'meal';
                $place = $meal->getPlace();
                $placeTags = $meal->getPlaceTags()->toArray();
            } else {
                // Group by date
                $takenAt = $media->getTakenAt();
                $dateKey = $takenAt?->format('Y-m-d') ?? 'no_date';
                $key      = 'date_' . $dateKey;
                $groupDate = $takenAt?->setTime(0, 0);
                $type     = 'date';
                $place = $media->getPlace();
                $placeTags = $media->getPlaceTags()->toArray();
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'type'   => $type,
                    'meal'   => $meal ?? null,
                    'date'   => $groupDate,
                    'medias' => [],
                    'place'  => $place,
                    'placeTags' => $placeTags,
                    'sort'   => $groupDate?->getTimestamp() ?? 0,
                ];
            }

            $groups[$key]['medias'][] = $media;
        }

        // Sort groups chronologically
        $groups = array_values($groups);
        usort($groups, static function (array $a, array $b): int {
            // b <=> a for newest first 
            // a <=> b for oldest first
            return $b['sort'] <=> $a['sort'];
        });

        return $groups;
    }
}
