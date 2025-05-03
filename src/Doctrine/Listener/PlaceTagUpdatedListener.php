<?php

namespace App\Doctrine\Listener;

use App\Entity\Tag\PlaceTag;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: PlaceTag::class, lazy: true)]
class PlaceTagUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(PlaceTag $tag, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($tag);
    }

    private function _updateSlugs(PlaceTag $tag)
    {
        $tag->setSlugFr($this->slugger->slug(\mb_strtolower($tag->getNameFr())));
        $tag->setSlugEn($this->slugger->slug(\mb_strtolower($tag->getNameEn())));
    }
}
