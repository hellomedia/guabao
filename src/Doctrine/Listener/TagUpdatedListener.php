<?php

namespace App\Doctrine\Listener;

use App\Entity\Tag\Tag;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: Tag::class, lazy: true)]
class TagUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(Tag $tag, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($tag);
    }

    private function _updateSlugs(Tag $tag)
    {
        $tag->setSlugFr($this->slugger->slug(\mb_strtolower($tag->getNameFr())));
        $tag->setSlugEn($this->slugger->slug(\mb_strtolower($tag->getNameEn())));
    }
}
