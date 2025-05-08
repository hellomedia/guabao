<?php

namespace App\Entity;

use App\Enum\MealType;
use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $enjoyedAt = null;

    #[ORM\Column(nullable: true, enumType: MealType::class)]
    private ?MealType $type = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'meal')]
    private Collection $pictures;

    #[ORM\ManyToOne(inversedBy: 'meals')]
    private ?Place $place = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnjoyedAt(): ?\DateTimeImmutable
    {
        return $this->enjoyedAt;
    }

    public function setEnjoyedAt(?\DateTimeImmutable $enjoyedAt): static
    {
        $this->enjoyedAt = $enjoyedAt;

        return $this;
    }

    public function getType(): ?MealType
    {
        return $this->type;
    }

    public function setType(?MealType $type): static
    {
        $this->type = $type;

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
            $picture->setMeal($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getMeal() === $this) {
                $picture->setMeal(null);
            }
        }

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->pictures?->first->getTrip();
    }
}
