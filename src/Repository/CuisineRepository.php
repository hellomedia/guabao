<?php

namespace App\Repository;

use App\Entity\Cuisine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Cuisine>
 */
class CuisineRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Cuisine::class);
    }

    public function findAll(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('c')
            ->orderBy('c.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithFoodCount(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $result = $this->createQueryBuilder('c')
            ->leftJoin('c.food', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS foodCount')
            ->groupBy('c.id')
            ->orderBy('c.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($row) => [
            'cuisine'    => $row[0],
            'food_count'  => (int)$row['foodCount'],
        ], $result);
    }
}
