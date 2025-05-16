<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Media;
use App\Entity\Trip;
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
        $medias = $this->createQueryBuilder('m')
            ->innerJoin('m.food', 'f')
            ->join('m.placeTags', 'pt')
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

    public function findByTrip(Trip $trip): array
    {
        $medias = $this->createQueryBuilder('m')
            ->leftJoin('m.placeTags', 'pt')
            ->addSelect('pt')
            ->leftJoin('m.tags', 't')
            ->addSelect('t')
            ->leftJoin('m.food', 'f')
            ->addSelect('f')
            ->where('m.trip = :trip')
            ->orderBy('m.takenAt', 'ASC')
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getResult();

        return $medias;
    }
}
