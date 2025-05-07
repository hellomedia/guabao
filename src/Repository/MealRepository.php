<?php

namespace App\Repository;

use App\Entity\Meal;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meal>
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

    public function findOneByPictureDate(DateTimeImmutable $pictureTakenAt)
    {
        $interval = new \DateInterval('PT10M');

        $start = $pictureTakenAt->sub($interval);
        $end = $pictureTakenAt->add($interval);

        return $this->createQueryBuilder('m')
            ->andWhere('m.enjoyedAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
