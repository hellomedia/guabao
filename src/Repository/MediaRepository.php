<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Food;
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

        // remove duplicate pics of same food on same day
        foreach ($medias as $media) {
            assert($media instanceof Media);
            $foodIds = $media->getFood()->map(fn(Food $food) => $food->getId());
            $dateKey = $media->getTakenAt()->format('Y-m-d');
            $groupKey = implode('_', $foodIds->toArray()) . '_' . $dateKey;

            if (!isset($seen[$groupKey])) {
                $seen[$groupKey] = true;
                $filtered[] = $media;
            }
        }

        return $filtered;
    }

    public function findByTrip(Trip $trip, ?bool $gallery = false, ?bool $adminList = false): array
    {
        return $this->getFindByTripQueryBuilder($trip, $gallery, $adminList)
            ->getQuery()
            ->getResult();
    }

    public function getFindByTripQueryBuilder(Trip $trip, ?bool $gallery = false, ?bool $adminList = false): QueryBuilder
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
            ->innerJoin('m.trip', 'trip')
            ->addSelect('trip')
            ->where('trip = :trip')
            ->setParameter('trip', $trip)
            ->orderBy('m.takenAt', 'ASC')
        ;

        if ($gallery) {
            $qb->andWhere('m.showInTrip = TRUE');
        }

        if ($adminList) {
            $qb->andWhere('m.story IS NULL');
        }

        return $qb;
    }

    public function findByStory(Story $story): array
    {
        return $this->getFindByStoryQueryBuilder($story)
            ->getQuery()
            ->getResult();
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
            ->innerJoin('m.story', 'story')
            ->addSelect('story')
            ->where('story = :story')
            ->setParameter('story', $story)
            ->orderBy('m.takenAt', 'ASC')
        ;
    }

    public function findByFood(Food $food): array
    {
        return $this->getFindByFoodQueryBuilder($food)
            ->getQuery()
            ->getResult();
    }

    public function getFindByFoodQueryBuilder(Food $food): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.trip', 'trip')
            ->addSelect('trip')
            ->leftJoin('m.placeTags', 'pt')
            ->addSelect('pt')
            ->leftJoin('m.tags', 't')
            ->addSelect('t')
            ->innerJoin('m.food', 'f')
            ->addSelect('f')
            ->where('f = :food') // 'where f = :food', not 'where m.food = food' . m.food is a collection. f is the linked food item
            ->setParameter('food', $food)
            ->leftJoin('m.meal', 'meal')
            ->addSelect('meal')
            ->leftJoin('m.story', 'story')
            ->addSelect('story')
            ->orderBy('m.takenAt', 'ASC')
        ;
    }

}
