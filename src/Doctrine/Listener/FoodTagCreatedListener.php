<?php

namespace App\Doctrine\Listener;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::prePersist, method: 'createSlugs', entity: FoodTag::class, lazy: true)]
class FoodTagCreatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function createSlugs(FoodTag $tag, PrePersistEventArgs $args)
    {
        $tag->setSlugFr($this->slugger->slug(\mb_strtolower($tag->getNameFr())));
        $tag->setSlugEn($this->slugger->slug(\mb_strtolower($tag->getNameEn())));
    }
}
