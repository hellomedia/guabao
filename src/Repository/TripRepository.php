<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\Tag\TripTag;
use App\Entity\Trip;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, Trip::class);
    }

    //    /**
    //     * @return Trip[] Returns an array of Trip objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', \SortDirection::Ascending)
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trip
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAll(?string $sort = null): array
    {
        $sortField = match ($sort) {
            'd' => 't.startedAt',
            'u' => 't.duration',
            'a' => 't.adventureRating',
            default => 't.startedAt',
        };
    
        return $this->createCardQueryBuilder()
            ->where('t.parent IS NULL')
            ->orderBy($sortField, \SortDirection::Descending)
            ->getQuery()
            ->getResult();
    }

    public function findAllByTag(TripTag $tag, ?string $sort): array
    {
        $sortField = match($sort) {
            'd' => 't.startedAt',
            'u' => 't.duration',
            'a' => 't.adventureRating',
            default => 't.startedAt',
        };

        // NB: 'tt' only filters. The full tag list comes from the fetch join
        // in createCardQueryBuilder(), under a different alias: selecting 'tt'
        // would leave only the matching tag in each trip's collection.
        return $this->createCardQueryBuilder()
            ->join('t.tags', 'tt')
            ->where('t.parent IS NULL')
            ->andWhere('tt.slugEn = :slugEn')
            ->orderBy($sortField, \SortDirection::Descending)
            ->setParameter('slugEn', $tag->getSlugEn())
            ->getQuery()
            ->getResult();
    }

    /**
     * Trip for the show pages, with the children (trip legs) and what their
     * cards display (cover, tags) already loaded.
     */
    public function findOneBySlug(string $slug): ?Trip
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('t')
            ->leftJoin('t.cover', 'cover')->addSelect('cover')
            ->leftJoin('t.children', 'child')->addSelect('child')
            ->leftJoin('child.cover', 'childCover')->addSelect('childCover')
            ->leftJoin('child.tags', 'childTag')->addSelect('childTag')
            ->where('t.slug' . \ucfirst($locale) . ' = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('child.startedAt', \SortDirection::Ascending)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Fetches everything trip/_card.html.twig reads (cover + tags),
     * so a list of cards costs one query instead of 1 + 2 per trip.
     */
    private function createCardQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.cover', 'cover')->addSelect('cover')
            ->leftJoin('t.tags', 'tag')->addSelect('tag');
    }

    public function findOneByMediaDate(?DateTimeImmutable $mediaTakenAt): ?Trip
    {
        if ($mediaTakenAt == null) {
            return null;
        }

        // if there are multiple trips for the date, it is lifely to be a leg and a parent trip.
        // If that is the case, we want to retrieve the leg ===> ORDER BY t.parent ASC NULLS LAST
        // and setMaxResults(1)
        return $this->createQueryBuilder('t')
            ->andWhere('t.startedAt < :mediaTakenAt')
            ->andWhere('t.endedAt > :mediaTakenAt')
            ->setParameter('mediaTakenAt', $mediaTakenAt)
            ->orderBy('t.parent', \SortDirection::Ascending) // default is NULLS LAST for ASC !
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPlaceTags(Trip $trip): Collection
    {
        $dql = <<<DQL
            SELECT DISTINCT pt, media
            FROM App\Entity\Media media
            JOIN media.placeTags pt
            WHERE media.trip = :trip
        DQL;

        /* root entity (pic) not selected first, so result is an array of [pt, pic] items */
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('trip', $trip);

        // build expected array collection of place tags
        return new ArrayCollection(array_map(fn($row) => $row['pt'], $query->getResult()));
    }

    public function findTopSlowTravelTrips(): array
    {
        return $this->findTopByHighlight('Favourite trips');
    }

    public function findTopHikingTrips(): array
    {
        return $this->findTopByHighlight('Favourite hiking');
    }

    private function findTopByHighlight(string $highlightName): array
    {
        return $this->createCardQueryBuilder()
            ->join('t.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('t.startedAt', \SortDirection::Descending)
            ->setParameter('highlightName', $highlightName)
            ->getQuery()
            ->getResult();
    }
}
