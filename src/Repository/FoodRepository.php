<?php

namespace App\Repository;

use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Food>
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Food::class);
    }

    public function findOneBySlug(string $slug): ?Food
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->findOneBy([
            'slug' . \ucfirst($locale) => $slug
        ]);
    }

    public function findByCuisine(Cuisine $cuisine): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->where('f.cuisine = :cuisine')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->setParameter('cuisine', $cuisine)
            ->getQuery()
            ->getResult();
    }

    public function findByFoodTag(FoodTag $tag): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.tags', 'ft')
            ->where('ft = :tag')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }

    public function findByIngredient(Ingredient $ingredient): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.ingredients', 'i')
            ->where('i = :ingredient')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->setParameter('ingredient', $ingredient)
            ->getQuery()
            ->getResult();
    }
}
