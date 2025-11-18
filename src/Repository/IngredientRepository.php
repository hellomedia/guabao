<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Ingredient>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Ingredient::class);
    }

    public function findAll(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('i')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithFoodCount(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $result = $this->createQueryBuilder('i')
            ->leftJoin('i.food', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS foodCount')
            ->groupBy('i.id')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($row) => [
            'ingredient'    => $row[0],
            'food_count'  => (int)$row['foodCount'],
        ], $result);
    }

    public function findFavourites(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('i')
            ->where('i.favourite = true')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFavouritesWithFoodCount(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $result = $this->createQueryBuilder('i')
            ->where('i.favourite = true')
            ->leftJoin('i.food', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS foodCount')
            ->groupBy('i.id')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($row) => [
            'ingredient'    => $row[0],
            'food_count'  => (int)$row['foodCount'],
        ], $result);
    }

    public function findNonFavourites(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('i')
            ->where('i.favourite != true')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNonFavouritesWithFoodCount(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $result = $this->createQueryBuilder('i')
            ->where('i.favourite != true')
            ->leftJoin('i.food', 'f')
            ->addSelect('COUNT(DISTINCT f.id) AS foodCount')
            ->groupBy('i.id')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($row) => [
            'ingredient'    => $row[0],
            'food_count'  => (int)$row['foodCount'],
        ], $result);
    }
}
