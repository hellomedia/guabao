<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SortDirection;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    public function findTopAdventures(): array
    {
        return $this->findTopByHighlight('Favourite adventures');
    }

    public function findTopDiscoveries(): array
    {
        return $this->findTopByHighlight('Favourite discoveries');
    }

    public function findTopHistoricalPlaces(): array
    {
        return $this->findTopByHighlight('Favourite historical places');
    }

    public function findTopPersonalExperiences(): array
    {
        return $this->findTopByHighlight('Favourite personal experiences');
    }

    /**
     * Stories tagged with a site highlight, newest trip first.
     *
     * The story card needs the trip (name + path) and, for a trip leg,
     * its parent (path), so both are fetched here to avoid one lazy load per story.
     * The story medias (cover) are fetch EAGER and batch-loaded by Doctrine.
     */
    private function findTopByHighlight(string $highlightName): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.trip', 't')->addSelect('t')
            ->leftJoin('t.parent', 'p')->addSelect('p')
            ->join('s.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('t.startedAt', SortDirection::Descending)
            ->setParameter('highlightName', $highlightName)
            ->getQuery()
            ->getResult();
    }
}
