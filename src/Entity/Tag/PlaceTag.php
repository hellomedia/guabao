<?php

namespace App\Entity\Tag;

use App\Entity\Country;
use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'place_tag')]
#[ORM\Entity]
class PlaceTag implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }
}
