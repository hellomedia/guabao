<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function findNearby(float $lat, float $lng, float $tolerance = 0.0010): ?Place
    {
        return $this->createQueryBuilder('p')
            ->where('ABS(p.latitude - :lat) < :tolerance')
            ->andWhere('ABS(p.longitude - :lng) < :tolerance')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->setParameter('tolerance', $tolerance) // roughly 50 meters
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
