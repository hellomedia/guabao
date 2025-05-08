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
}
