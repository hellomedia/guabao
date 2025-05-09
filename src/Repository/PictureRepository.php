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

    public function findFoodPicturesByCountry(Country $country): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        // contains duplicate food pics (2 food pics of the same food taken during a meal)
        $pictures = $this->createQueryBuilder('p')
            ->innerJoin('p.food', 'f')
            ->join('p.placeTags', 'pt')
            ->where('pt.country = :country')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        $seen = [];
        $filtered = [];

        // remove dupliplcate pics of same food on same day
        foreach ($pictures as $pic) {
            $foodId = $pic->getFood()->getId();
            $dateKey = $pic->getTakenAt()->format('Y-m-d');
            $groupKey = $foodId . '_' . $dateKey;

            if (!isset($seen[$groupKey])) {
                $seen[$groupKey] = true;
                $filtered[] = $pic;
            }
        }

        return $filtered;
    }
}
