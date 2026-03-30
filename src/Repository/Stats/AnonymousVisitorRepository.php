<?php

namespace App\Repository\Stats;

use App\Entity\Stats\AnonymousVisitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AnonymousVisitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnonymousVisitor::class);
    }

    public function findNotableVisitorsOrderedByLastSeen(): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->andWhere('v.pageCount > 2')
            ->orderBy('v.lastSeenAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findNotableVisitorsOrderedByPageCount(): array
    {
        return $this->createQueryBuilder('v')
            ->select('v')
            ->andWhere('v.pageCount > 2')
            ->orderBy('v.pageCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
