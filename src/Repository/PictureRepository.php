<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Picture>
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private RequestStack $requestStack
    ) {
        parent::__construct($registry, Picture::class);
    }

    public function findFoodPictureByCountry(Country $country): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('p')
            ->innerJoin('p.food', 'f')
            ->join('p.placeTags', 'pt')
            ->where('pt.country = :country')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();
    }
}
