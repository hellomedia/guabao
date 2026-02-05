<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        return $this->createQueryBuilder('s')
            ->join('s.trip', 't')
            ->join('s.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('t.startedAt', 'DESC')
            ->setParameter('highlightName', 'Favourite adventures')
            ->getQuery()
            ->getResult();
    }

    public function findTopDiscoveries(): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.trip', 't')
            ->join('s.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('t.startedAt', 'DESC')
            ->setParameter('highlightName', 'Favourite discoveries')
            ->getQuery()
            ->getResult();
    }
}
