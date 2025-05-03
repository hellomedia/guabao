<?php

namespace App\Doctrine\Listener;

use App\Entity\FoodItem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: FoodItem::class, lazy: true)]
class FoodItemUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(FoodItem $food, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($food);
    }

    private function _updateSlugs(FoodItem $food)
    {
        $food->setSlugFr($this->slugger->slug(\mb_strtolower($food->getNameFr())));
        $food->setSlugEn($this->slugger->slug(\mb_strtolower($food->getNameEn())));
    }
}
