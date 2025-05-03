<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Trip;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findPlaceTags(Trip $trip): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT pt')
            ->from(Picture::class, 'pic')
            ->join('pic.placeTags', 'pt')
            ->where('pic.trip = :trip')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getResult();
    }

    public function findCountries(Trip $trip): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT c')
            ->from(Picture::class, 'pic')
            ->join('pic.placeTags', 'pt')
            ->join('pt.country', 'c')
            ->where('pic.trip = :trip')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getResult();
    }
}
