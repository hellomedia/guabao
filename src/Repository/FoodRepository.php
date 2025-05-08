<?php

namespace App\Repository;

use App\Entity\Food;
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
}
