<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private RequestStack $requestStack
    ) {
        parent::__construct($registry, Media::class);
    }

    public function findFoodMediasByCountry(Country $country): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        // contains duplicate food pics (2 food pics of the same food taken during a meal)
        $medias = $this->createQueryBuilder('p')
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
        foreach ($medias as $media) {
            $foodId = $media->getFood()->getId();
            $dateKey = $media->getTakenAt()->format('Y-m-d');
            $groupKey = $foodId . '_' . $dateKey;

            if (!isset($seen[$groupKey])) {
                $seen[$groupKey] = true;
                $filtered[] = $media;
            }
        }

        return $filtered;
    }
}
