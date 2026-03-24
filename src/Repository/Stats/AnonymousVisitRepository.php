<?php

namespace App\Repository\Stats;

use App\Entity\Stats\AnonymousVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AnonymousVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnonymousVisit::class);
    }

    public function hasPreviousVisitForVisitorId(string $visitorId): bool
    {
        return null !== $this->createQueryBuilder('v')
            ->select('v.id')
            ->andWhere('v.visitorId = :visitorId')
            ->setParameter('visitorId', $visitorId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
