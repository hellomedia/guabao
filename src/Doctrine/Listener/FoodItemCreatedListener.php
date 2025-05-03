<?php

namespace App\Doctrine\Listener;

use App\Entity\FoodItem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::prePersist, method: 'createSlugs', entity: FoodItem::class, lazy: true)]
class FoodItemCreatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function createSlugs(FoodItem $food, PrePersistEventArgs $args)
    {
        $food->setSlugFr($this->slugger->slug(\mb_strtolower($food->getNameFr())));
        $food->setSlugEn($this->slugger->slug(\mb_strtolower($food->getNameEn())));
    }
}
