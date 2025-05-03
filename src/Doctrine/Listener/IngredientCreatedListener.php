<?php

namespace App\Doctrine\Listener;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::prePersist, method: 'createSlugs', entity: Ingredient::class, lazy: true)]
class IngredientCreatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function createSlugs(Ingredient $ingredient, PrePersistEventArgs $args)
    {
        $ingredient->setSlugFr($this->slugger->slug(\mb_strtolower($ingredient->getNameFr())));
        $ingredient->setSlugEn($this->slugger->slug(\mb_strtolower($ingredient->getNameEn())));
    }
}
