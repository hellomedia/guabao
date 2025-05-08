<?php

namespace App\Repository;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<FoodTag>
 */
class FoodTagRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, FoodTag::class);
    }

    public function findAll(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('ft')
            ->orderBy('ft.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }
}
