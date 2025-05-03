<?php

namespace App\Doctrine\Listener;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: FoodTag::class, lazy: true)]
class FoodTagUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(FoodTag $tag, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($tag);
    }

    private function _updateSlugs(FoodTag $tag)
    {
        $tag->setSlugFr($this->slugger->slug(\mb_strtolower($tag->getNameFr())));
        $tag->setSlugEn($this->slugger->slug(\mb_strtolower($tag->getNameEn())));
    }
}
