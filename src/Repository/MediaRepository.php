<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Food;
use App\Entity\Media;
use App\Entity\Story;
use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
            ->innerJoin('m.food', 'food')->addSelect('food')
            ->innerJoin('m.placeTags', 'pt')->addSelect('pt')
            ->leftJoin('m.place', 'place')->addSelect('place')
            ->where('pt.country = :country')
            ->setParameter('country', $country)
            // optimization
            ->leftJoin('m.meal', 'meal')->addSelect('meal')
            ->leftJoin('meal.placeTags', 'placeTags')->addSelect('placeTags')
            ->orderBy('m.takenAt', 'DESC')
            ->getQuery()
            ->getResult();

        $seen = [];
        $filtered = [];

        // return $medias;

        // // remove duplicate pics of same food on same day
        // foreach ($medias as $media) {
        //     assert($media instanceof Media);
        //     $foodIds = $media->getFood()->map(fn(Food $food) => $food->getId());
        //     $dateKey = $media->getTakenAt()->format('Y-m-d');
        //     $groupKey = implode('_', $foodIds->toArray()) . '_' . $dateKey;

        //     if (!isset($seen[$groupKey])) {
        //         $seen[$groupKey] = true;
        //         $filtered[] = $media;
        //     }
        // }

        // return $filtered;


        $groups = [];

        foreach ($medias as $media) {
            $meal = $media->getMeal();

            if ($meal !== null) {
                // Group by meal (takes precedence)
                $key = 'meal_' . $meal->getId();
                // Adjust these according to your Meal entity
                $groupDate = $meal->getEnjoyedAt();
                $type  = 'meal';
                $place = $meal->getPlace();
                $placeTags = $meal->getPlaceTags()->toArray();
            } else {
                // Group by date
                $takenAt = $media->getTakenAt();
                $dateKey = $takenAt?->format('Y-m-d') ?? 'no_date';
                $key      = 'date_' . $dateKey;
                $groupDate = $takenAt?->setTime(0, 0);
                $type     = 'date';
                $place = $media->getPlace();
                $placeTags = $media->getPlaceTags()->toArray();
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'type'   => $type,
                    'meal'   => $meal ?? null,
                    'date'   => $groupDate,
                    'medias' => [],
                    'place'  => $place,
                    'placeTags' => $placeTags,
                    'sort'   => $groupDate?->getTimestamp() ?? 0,
                ];
            }

            $groups[$key]['medias'][] = $media;
        }

        // Sort groups chronologically
        $groups = array_values($groups);
        usort($groups, static function (array $a, array $b): int {
            // b <=> a for newest first 
            // a <=> b for oldest first
            return $b['sort'] <=> $a['sort'];
        });

        return $groups;
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

    /**
     * Media not linked to a trip / food / meal / story
     * 
     * Use case: upload medias not linked to a trip, for food.
     * ===> find them and handle them (assign to food)
     */
    public function findUnliked(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.placeTags', 'pt')
            ->addSelect('pt')
            ->leftJoin('m.tags', 't')
            ->addSelect('t')
            ->where('m.trip IS NULL')
            ->andWhere('m.story IS NULL')
            ->andWhere('m.food IS EMPTY') // m.food is a collection
            ->andWhere('m.meal IS NULL')
            ->orderBy('m.takenAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

}
