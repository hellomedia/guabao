<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
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

    public function findByTrip(Trip $trip, ?bool $onlyDefaultGallery = false): Collection
    {
        $query = $this->getFindByTripQueryBuilder($trip, $onlyDefaultGallery)
            ->getQuery();

        return new ArrayCollection($query->getResult());
    }

    public function getFindByTripQueryBuilder(Trip $trip, ?bool $onlyDefaultGallery = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.story', 's')
            ->addSelect('s')
            ->leftJoin('m.placeTags', 'pt')
            ->addSelect('pt')
            ->leftJoin('m.tags', 't')
            ->addSelect('t')
            ->leftJoin('m.food', 'f')
            ->addSelect('f')
            ->leftJoin('m.meal', 'meal')
            ->addSelect('meal')
            ->leftJoin('m.trip', 'trip')
            ->addSelect('trip')
            ->where('m.trip = :trip')
            ->setParameter('trip', $trip)
            ->orderBy('m.takenAt', 'ASC')
        ;

        if ($onlyDefaultGallery) {
            // do not display media who should not display
            // in default gallery if they belong to a story
            $qb->andWhere('m.inDefaultGallery = TRUE OR m.story IS NULL');
        }

        return $qb;
    }

    public function findByStory(Story $story): Collection
    {
        $query = $this->getFindByStoryQueryBuilder($story)
            ->getQuery();

        return new ArrayCollection($query->getResult());
    }

    public function getFindByStoryQueryBuilder(Story $story): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.trip', 'trip')
            ->addSelect('trip')
            ->leftJoin('m.placeTags', 'pt')
            ->addSelect('pt')
            ->leftJoin('m.tags', 't')
            ->addSelect('t')
            ->leftJoin('m.food', 'f')
            ->addSelect('f')
            ->leftJoin('m.meal', 'meal')
            ->addSelect('meal')
            ->leftJoin('m.story', 'story')
            ->addSelect('story')
            ->where('m.story = :story')
            ->setParameter('story', $story)
            ->orderBy('m.takenAt', 'ASC')
        ;
    }

}
