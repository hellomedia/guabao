<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Trip;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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

    public function findOneByPictureDate(DateTimeImmutable $pictureTakenAt)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.startedAt < :pictureTakenAt')
            ->andWhere('t.endedAt > :pictureTakenAt')
            ->setParameter('pictureTakenAt', $pictureTakenAt)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPictures(Trip $trip): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pic')
            ->from(Picture::class, 'pic')
            ->where('pic.trip = :trip')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getResult();
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

    public function findCountries(Trip $trip): Collection
    {
        $dql = <<<DQL
            SELECT DISTINCT c, pic
            FROM App\Entity\Picture pic
            JOIN pic.placeTags pt
            JOIN pt.country c
            WHERE pic.trip = :trip
        DQL;

        /* root entity (pic) not selected first, so result is an array of [pt, pic] items */
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('trip', $trip);

        // build expected array collection of place tags
        return new ArrayCollection(array_map(fn($row) => $row['c'], $query->getResult()));
    }

    public function findCover(Trip $trip): ?Picture
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pic')
            ->from(Picture::class, 'pic')
            ->where('pic.trip = :trip')
            ->andWhere('pic.cover = true')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
