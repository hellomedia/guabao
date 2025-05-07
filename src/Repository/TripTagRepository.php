<?php

namespace App\Repository;

use App\Entity\Tag\TripTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<TripTag>
 */
class TripTagRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, TripTag::class);
    }

    public function findOneBySlug(string $slug): ?TripTag
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->findOneBy([
            'slug' . \ucfirst($locale) => $slug
        ]);
    }
}
