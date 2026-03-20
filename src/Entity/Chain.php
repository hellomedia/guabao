<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Interface\OriginalNameInterface;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Entity\Trait\OriginalNameTrait;
use App\Repository\ChainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChainRepository::class)]
class Chain implements EntityInterface, LocalizedNameInterface, LocalizedSlugInterface, OriginalNameInterface
{
    use LocalizedNameTrait;

    use LocalizedDescriptionTrait;

    use OriginalNameTrait;

    use LocalizedSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $favourite = false;

    /**
     * @var Collection<int, Place>
     */
    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'chain')]
    private Collection $places;

    public function __construct()
    {
        $this->places = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFavourite(): bool
    {
        return $this->favourite;
    }

    public function setFavourite(bool $favourite): static
    {
        $this->favourite = $favourite;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
            $place->setChain($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getChain() === $this) {
                $place->setChain(null);
            }
        }

        return $this;
    }
}
