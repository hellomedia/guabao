<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->leftJoin('f.cuisines', 'c')
            ->addSelect('c')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    public function findOneBySlug(string $slug): ?Food
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->findOneBy([
            'slug' . \ucfirst($locale) => $slug
        ]);
    }

    public function findByCuisine(Cuisine $cuisine): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        return $this->createQueryBuilder('f')
            ->join('f.cuisines', 'c')
            ->where('c = :cuisine')
            ->setParameter('cuisine', $cuisine)
            ->leftJoin('f.cover', 'cover')
            ->addSelect('cover')
            ->leftJoin('f.tags', 'tag')
            ->addSelect('tag')
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
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
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
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
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
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
            ->orderBy('f.name' . \ucfirst($locale), 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTopDiscoveries(): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.siteHighlights', 'h')
            ->where('h.nameEn = :highlightName')
            ->orderBy('f.nameEn', 'ASC')
            ->setParameter('highlightName', 'Favourite discoveries')
            ->getQuery()
            ->getResult();
    }
}
