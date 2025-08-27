<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Enum\FoodType;
use App\Enum\Month;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ingredient')]
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

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

    public function __construct()
    {
        $this->food = new ArrayCollection();
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
}
