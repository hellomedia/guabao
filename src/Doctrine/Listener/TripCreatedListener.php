<?php

namespace App\Doctrine\Listener;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 * Example of EntityListener
 * Already covered by SluggableListener which applies to all entities
 */
#[AsEntityListener(event: Events::prePersist, method: 'createKey', entity: Trip::class, lazy: true)]
class TripCreatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function createKey(Trip $trip, PrePersistEventArgs $args)
    {
        if ($trip->getKey() != null) {
            return;
        }

        $trip->setKey($this->slugger->slug(\mb_strtolower($trip->getNameEn())));
    }
}
