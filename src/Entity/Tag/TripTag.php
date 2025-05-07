<?php

namespace App\Entity\Tag;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use App\Entity\Trait\KeyTrait;
use App\Entity\Trait\LocalizedDescriptionTrait;
use App\Entity\Trait\LocalizedNameTrait;
use App\Entity\Trait\LocalizedSlugTrait;
use App\Repository\TripTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'trip_tag')]
#[ORM\Entity(repositoryClass: TripTagRepository::class)]
class TripTag implements LocalizedNameInterface, LocalizedSlugInterface, EntityInterface
{
    use KeyTrait;

    use LocalizedNameTrait;

    use LocalizedSlugTrait;

    use LocalizedDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
