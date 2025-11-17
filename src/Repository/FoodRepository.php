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

    public function findAll(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('i')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->orderBy('i.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
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
            ->join('f.cuisines', 'c')
            ->where('c = :cuisine')
            ->setParameter('cuisine', $cuisine)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFoodTag(FoodTag $tag): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.tags', 'ft')
            ->where('ft = :tag')
            ->setParameter('tag', $tag)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByIngredient(Ingredient $ingredient): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.ingredients', 'i')
            ->where('i = :ingredient')
            ->setParameter('ingredient', $ingredient)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }
}
