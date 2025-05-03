<?php

namespace App\Doctrine\Listener;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: Ingredient::class, lazy: true)]
class IngredientUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(Ingredient $ingredient, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($ingredient);
    }

    private function _updateSlugs(Ingredient $ingredient)
    {
        $ingredient->setSlugFr($this->slugger->slug(\mb_strtolower($ingredient->getNameFr())));
        $ingredient->setSlugEn($this->slugger->slug(\mb_strtolower($ingredient->getNameEn())));
    }
}
