<?php

namespace App\Doctrine\Listener;

use App\Entity\Interface\HasPeriodInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
class SluggableListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {}

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof LocalizedSlugInterface) {
            return;
        }

        if (!$entity instanceof LocalizedNameInterface) {
            return;
        }

        $this->_setLocalizedSlugs($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof LocalizedSlugInterface) {
            return;
        }

        if (!$entity instanceof LocalizedNameInterface) {
            return;
        }

        $this->_setLocalizedSlugs($entity);
    }

    private function _setLocalizedSlugs(LocalizedSlugInterface $entity)
    {
        assert($entity instanceof LocalizedNameInterface);

        if ($entity instanceof HasPeriodInterface) {
            $entity->setSlugFr($this->slugger->slug(\mb_strtolower($entity->getNameFr() . ' ' . $entity->getPeriod())));
            $entity->setSlugEn($this->slugger->slug(\mb_strtolower($entity->getNameEn() . ' ' . $entity->getPeriod())));

            return;
        }

        $entity->setSlugFr($this->slugger->slug(\mb_strtolower($entity->getNameFr())));
        $entity->setSlugEn($this->slugger->slug(\mb_strtolower($entity->getNameEn())));
    }
}
