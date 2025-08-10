<?php

namespace App\Doctrine\Listener;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 * Example of EntityListener
 * Already covered by SluggableListener which applies to all entities
 */
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Trip::class, lazy: true)]
class TripUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    // NB: only goes through here if something else is updated
    public function preUpdate(Trip $trip, PreUpdateEventArgs $args)
    {
        if ($trip->getDuration() == null) {
            $interval = $trip->getEndedAt()->diff($trip->getStartedAt());
            $trip->setDuration($interval->days);
        }
    }
}
