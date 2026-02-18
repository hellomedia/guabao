<?php

namespace App\Doctrine\Listener;

use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\SearchableNameInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
class SearchableNameListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {}

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof SearchableNameInterface) {
            return;
        }

        $this->_setNameSearch($entity);
    }

    // NB: only goes through here if something else is updated
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof SearchableNameInterface) {
            return;
        }

        $this->_setNameSearch($entity);
    }

    private function _setNameSearch(SearchableNameInterface $entity)
    {
        if (!$entity instanceof LocalizedNameInterface) {
            return;
        }

        assert($entity instanceof SearchableNameInterface);
        assert($entity instanceof LocalizedNameInterface);

        $text = trim(mb_strtolower($entity->getNameFr() . ' ' . $entity->getNameEn()));
        $normalized = (string) $this->slugger->slug($text, ' ');

        $entity->setNameSearch($normalized);
    }
}
