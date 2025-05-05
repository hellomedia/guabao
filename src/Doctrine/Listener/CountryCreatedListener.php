<?php

namespace App\Doctrine\Listener;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 * Example of EntityListener
 * Already covered by SluggableListener which applies to all entities
 */
#[AsEntityListener(event: Events::prePersist, method: 'createSlugs', entity: Country::class, lazy: true)]
class CountryCreatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function createSlugs(Country $country, PrePersistEventArgs $args)
    {
        // already covered by SluggableListener which applies to all entities

        // $country->setSlugFr($this->slugger->slug(\mb_strtolower($country->getNameFr())));
        // $country->setSlugEn($this->slugger->slug(\mb_strtolower($country->getNameEn())));
    }
}
