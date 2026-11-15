<?php

namespace App\Doctrine\Listener;

use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Interface\HasPeriodInterface;
use App\Entity\Interface\LocalizedNameInterface;
use App\Entity\Interface\LocalizedSlugInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Connection;
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

        if ($entity instanceof Food) {
            $this->makeFoodSlugsUnique($entity, null, $args->getObjectManager()->getConnection());
        }
    }

    // NB: only goes through here if something else is updated
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

        if ($entity instanceof Food) {
            $this->makeFoodSlugsUnique($entity, $entity->getId(), $args->getObjectManager()->getConnection());
        }
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

    /**
     * Two foods can share a name (a Thai and a Chinese "Fried rice") but not a slug: the food
     * pages look a food up by slug (unique indexes on food.slug_en and food.slug_fr).
     * Keeps the plain slug when it is free, else appends a cuisine slug, else a number:
     * fried-rice, fried-rice-thai, fried-rice-2.
     * Same rule as migration Version20261115120000, which renamed the existing duplicates.
     */
    private function makeFoodSlugsUnique(Food $food, ?int $foodId, Connection $connection): void
    {
        $cuisines = $food->getCuisines()->toArray();
        usort($cuisines, fn (Cuisine $a, Cuisine $b) => $a->getId() <=> $b->getId());

        foreach (['en', 'fr'] as $locale) {
            $base = (string) $food->getSlug($locale);
            // Slugger output only holds [a-z0-9-], no LIKE wildcard to escape
            $taken = $connection->fetchFirstColumn(
                'SELECT slug_' . $locale . ' FROM food WHERE slug_' . $locale . ' LIKE :prefix'
                . ($foodId !== null ? ' AND id <> :id' : ''),
                ['prefix' => $base . '%'] + ($foodId !== null ? ['id' => $foodId] : []),
            );

            $candidates = [$base];
            foreach ($cuisines as $cuisine) {
                $candidates[] = $base . '-' . $cuisine->getSlug($locale);
            }
            for ($n = 2;; ++$n) {
                $candidates[] = $base . '-' . $n;
                if (count($candidates) > count($taken)) {
                    break;
                }
            }

            $slug = array_values(array_diff($candidates, $taken))[0];
            $locale === 'en' ? $food->setSlugEn($slug) : $food->setSlugFr($slug);
        }
    }
}
