<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Country::class);
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
            ->innerJoin('c.placeTags', 'pt')
            ->innerJoin('pt.medias', 'm')
            ->innerJoin('m.food', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS foodCount')
            ->groupBy('c.id')
            ->orderBy('c.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($row) => [
            'country'    => $row[0],
            'food_count'  => (int)$row['foodCount'],
        ], $result);
    }
}
