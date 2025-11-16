<?php

namespace App\Repository;

use App\Entity\Tag\PlaceTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<PlaceTag>
 */
class PlaceTagRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, PlaceTag::class);
    }

    public function findAll(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('pt')
            ->orderBy('pt.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }
}
