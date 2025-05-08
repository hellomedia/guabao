<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Tag\TripTag;
use App\Entity\Trip;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    //            ->orderBy('t.id', 'ASC')
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

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByTag(TripTag $tag): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tags', 'tt')
            ->where('tt.slugEn = :slugEn')
            ->orderBy('t.startedAt', 'DESC')
            ->setParameter('slugEn', $tag->getSlugEn())
            ->getQuery()
            ->getResult();
    }

    public function findOneBySlug(string $slug): ?Trip
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->findOneBy([
            'slug' . \ucfirst($locale) => $slug
        ]);
    }

    public function findOneByPictureDate(DateTimeImmutable $pictureTakenAt)
    {
        
        return $this->createQueryBuilder('t')
            ->andWhere('t.startedAt < :pictureTakenAt')
            ->andWhere('t.endedAt > :pictureTakenAt')
            ->setParameter('pictureTakenAt', $pictureTakenAt)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPictures(Trip $trip): Collection
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pic')
            ->from(Picture::class, 'pic')
            ->where('pic.trip = :trip')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($query->getResult());
    }

    public function findPlaceTags(Trip $trip): Collection
    {
        $dql = <<<DQL
            SELECT DISTINCT pt, pic
            FROM App\Entity\Picture pic
            JOIN pic.placeTags pt
            WHERE pic.trip = :trip
        DQL;

        /* root entity (pic) not selected first, so result is an array of [pt, pic] items */
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('trip', $trip);

        // build expected array collection of place tags
        return new ArrayCollection(array_map(fn($row) => $row['pt'], $query->getResult()));
    }
}
