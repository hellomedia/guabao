<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Meal;
use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Food>
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(
        private RequestStack $requestStack,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Food::class);
    }

    public function findAll(): array
    {
        // NB: Set default for command line where currentRequest() is null
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';

        return $this->createQueryBuilder('f')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->leftJoin('f.cuisines', 'c')
            ->addSelect('c')
            ->orderBy('f.name' . \ucfirst($locale), \SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySlug(string $slug): ?Food
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->findOneBy([
            'slug' . \ucfirst($locale) => $slug,
        ]);
    }

    /**
     * Food for the detail page (current locale slug), see createShowQueryBuilder().
     */
    public function findOneBySlugWithMedias(string $slug): ?Food
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $food = $this->createShowQueryBuilder()
            ->where('f.slug' . \ucfirst($locale) . ' = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->loadMealGraph($food);
    }

    /**
     * Food for the detail page (English slug, used by the tag / cuisine / ingredient routes).
     */
    public function findOneBySlugEnWithMedias(string $slugEn): ?Food
    {
        $food = $this->createShowQueryBuilder()
            ->where('f.slugEn = :slugEn')
            ->setParameter('slugEn', $slugEn)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->loadMealGraph($food);
    }

    /**
     * Fetches the media graph that Food::getMediaGroups() and food/show.html.twig walk
     * (media -> meal / place / placeTags -> country), so the detail page runs a fixed
     * number of queries instead of 3 per media.
     * Meals are completed by loadMealGraph() in a second query to keep this one small.
     */
    private function createShowQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.cover', 'cover')->addSelect('cover')
            ->leftJoin('f.medias', 'm')->addSelect('m')
            ->leftJoin('m.meal', 'meal')->addSelect('meal')
            ->leftJoin('m.place', 'place')->addSelect('place')
            ->leftJoin('m.placeTags', 'pt')->addSelect('pt')
            ->leftJoin('pt.country', 'ptCountry')->addSelect('ptCountry');
    }

    /**
     * Loads, in one query, what getMediaGroups() reads on the meals of a food:
     * all their medias (a meal group shows every meal media), place and place tags.
     * The meals are already managed, Doctrine fills their collections from the fetch join.
     */
    private function loadMealGraph(?Food $food): ?Food
    {
        if ($food === null) {
            return null;
        }

        $meals = [];
        foreach ($food->getMedias() as $media) {
            if ($media->getMeal() !== null) {
                $meals[$media->getMeal()->getId()] = $media->getMeal();
            }
        }

        if ($meals === []) {
            return $food;
        }

        $this->getEntityManager()->createQueryBuilder()
            ->select('meal, mealMedia, mealPlace, mealPlaceTag, mealPlaceTagCountry')
            ->from(Meal::class, 'meal')
            ->leftJoin('meal.medias', 'mealMedia')
            ->leftJoin('meal.place', 'mealPlace')
            ->leftJoin('meal.placeTags', 'mealPlaceTag')
            ->leftJoin('mealPlaceTag.country', 'mealPlaceTagCountry')
            ->where('meal IN (:meals)')
            ->setParameter('meals', array_values($meals))
            ->getQuery()
            ->getResult();

        return $food;
    }

    public function findByCuisine(Cuisine $cuisine): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        // NB: 'c' only filters; the card lists all cuisines of each food,
        // which come from the 'cuisine' fetch join.
        return $this->createQueryBuilder('f')
            ->join('f.cuisines', 'c')
            ->where('c = :cuisine')
            ->setParameter('cuisine', $cuisine)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->leftJoin('f.cuisines', 'cuisine')
            ->addSelect('cuisine')
            ->orderBy('f.name' . \ucfirst($locale), \SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    public function findByFoodTag(FoodTag $tag): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            // filter food on tag
            ->innerJoin('f.tags', 'ft')
            ->where('ft = :tag')
            ->setParameter('tag', $tag)
            // for selected food, also get other tags
            // NB: note the different alias
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.cuisines', 'cuisine')
            ->addSelect('cuisine')
            ->orderBy('f.name' . \ucfirst($locale), \SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    public function findByIngredient(Ingredient $ingredient): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.ingredients', 'i')
            ->where('i = :ingredient')
            ->setParameter('ingredient', $ingredient)
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.cuisines', 'cuisine')
            ->addSelect('cuisine')
            ->orderBy('f.name' . \ucfirst($locale), \SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    public function findByCountry(Country $country): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->innerJoin('f.medias', 'm')
            ->innerJoin('m.placeTags', 'pt')
            ->innerJoin('pt.country', 'c')
            ->where('c = :country')
            ->setParameter('country', $country)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->orderBy('f.name' . \ucfirst($locale), \SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    public function findTopDiscoveries(): array
    {
        // cover + cuisines: what the short food card displays
        return $this->createQueryBuilder('f')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.cuisines', 'cuisine')
            ->addSelect('cuisine')
            ->join('f.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('f.nameEn', \SortDirection::Ascending)
            ->setParameter('highlightName', 'Favourite discoveries')
            ->getQuery()
            ->getResult();
    }
}
