<?php

namespace App\Repository\Stats;

use App\Entity\Stats\AnonymousVisit;
use App\Entity\Stats\AnonymousVisitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AnonymousVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnonymousVisit::class);
    }

    public function hasPreviousVisitForVisitor(AnonymousVisitor $visitor): bool
    {
        return null !== $this->createQueryBuilder('v')
            ->select('v.id')
            ->andWhere('v.visitor = :visitor')
            ->setParameter('visitor', $visitor)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
