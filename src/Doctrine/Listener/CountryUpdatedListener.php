<?php

namespace App\Doctrine\Listener;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::preUpdate, method: 'update', entity: Country::class, lazy: true)]
class CountryUpdatedListener
{
    public function __construct(
        private SluggerInterface $slugger,
    )
    {
    }

    public function update(Country $country, PreUpdateEventArgs $args)
    {
        $this->_updateSlugs($country);
    }

    private function _updateSlugs(Country $country)
    {
        $country->setSlugFr($this->slugger->slug(\mb_strtolower($country->getNameFr())));
        $country->setSlugEn($this->slugger->slug(\mb_strtolower($country->getNameEn())));
    }
}
