<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Interface\SearchableNameInterface;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Entity\Trait\SearchableNameTrait;
use App\Enum\FoodType;
use App\Enum\Month;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ingredient')]
#[ORM\Index(name: 'ingredient_name_search_idx', columns: ['name_search'])]
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient implements LocalizedNameInterface, LocalizedSlugInterface, SearchableNameInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedDescriptionTrait;

    use LocalizedSlugTrait;

    use SearchableNameTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $favourite = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\ManyToMany(targetEntity: Food::class, mappedBy: 'ingredients')]
    private Collection $food;

    #[ORM\Column(enumType: Month::class, nullable: true)]
    private ?Month $seasonStart = null;

    #[ORM\Column(enumType: Month::class, nullable: true)]
    private ?Month $seasonEnd = null;

    #[ORM\Column(nullable: true, enumType: FoodType::class)]
    private ?FoodType $foodType = null;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'similarTo')]
    #[ORM\JoinTable(name: 'ingredient_similar')]
    private Collection $similar;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class, mappedBy: 'similar')]
    private Collection $similarTo;

    public function __construct()
    {
        $this->food = new ArrayCollection();
        $this->similar = new ArrayCollection();
        $this->similarTo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Food>
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): static
    {
        if (!$this->food->contains($food)) {
            $this->food->add($food);
            $food->addIngredient($this);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        if ($this->food->removeElement($food)) {
            $food->removeIngredient($this);
        }

        return $this;
    }

    public function getFoodType(): ?FoodType
    {
        return $this->foodType;
    }

    public function setFoodType(?FoodType $foodType): static
    {
        $this->foodType = $foodType;

        return $this;
    }

    public function addSimilar(Ingredient $ingredient): void
    {
        if (!$this->similar->contains($ingredient)) {
            $this->similar->add($ingredient);
            $ingredient->addSimilar($this); // keep in sync
        }
    }

    public function removeSimilar(Ingredient $ingredient): void
    {
        if ($this->similar->removeElement($ingredient)) {
            $ingredient->removeSimilar($this); // keep in sync
        }
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getSimilar(): Collection
    {
        return $this->similar;
    }

}
