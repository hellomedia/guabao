<?php

namespace App\Repository;

use App\Entity\VideoTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoTag[]    findAll()
 * @method VideoTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoTag::class);
    }

    // /**
    //  * @return VideoTag[] Returns an array of VideoTag objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoTag
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
