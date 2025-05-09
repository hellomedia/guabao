<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Tag\FoodTag;
use App\Repository\CountryRepository;
use App\Repository\CuisineRepository;
use App\Repository\FoodRepository;
use App\Repository\FoodTagRepository;
use App\Repository\IngredientRepository;
use App\Repository\PictureRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
        $this->addBreadcrumb('food.index', 'food_index');
    }

    #[Route('/food', name: 'food_index')]
    public function index(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findAll();

        return $this->render('food/index_by_country.html.twig', [
            'countries' => $countries
        ]);
    }

    #[Route('/food/country', name: 'food_index_by_country')]
    public function indexByCountry(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findAll();

        $this->addBreadcrumb('food.by_country');
        
        return $this->render('food/index_by_country.html.twig', [
            'countries' => $countries
        ]);
    }

    #[Route('/food/cuisine', name: 'food_index_by_cuisine')]
    public function indexByCuisine(CuisineRepository $cuisineRepository): Response
    {
        $cuisines = $cuisineRepository->findAll();

        $this->addBreadcrumb('food.by_cuisine');

        return $this->render('food/index_by_cuisine.html.twig', [
            'cuisines' => $cuisines
        ]);
    }

    #[Route('/food/tag', name: 'food_index_by_tag')]
    public function indexByTag(FoodTagRepository $foodTagRepository): Response
    {
        $tags = $foodTagRepository->findAll();

        $this->addBreadcrumb('food.by_type');

        return $this->render('food/index_by_tag.html.twig', [
            'tags' => $tags
        ]);
    }

    #[Route('/food/ingredient', name: 'food_index_by_ingredient')]
    public function indexByIngredient(IngredientRepository $ingredientRepository): Response
    {
        $favourites = $ingredientRepository->findFavourites();
        $others = $ingredientRepository->findNonFavourites();

        $this->addBreadcrumb('food.by_ingredient');

        return $this->render('food/index_by_ingredient.html.twig', [
            'favourites' => $favourites,
            'others' => $others,
        ]);
    }

    #[Route('/food/country/{slugEn:country}', name: 'food_by_country')]
    public function foodByCountry(Country $country, PictureRepository $pictureRepository, Request $request): Response
    {
        $pictures = $pictureRepository->findFoodPicturesByCountry($country);

        $this->addBreadcrumb('food.by_country', 'food_index_by_country');
        $this->addBreadcrumb($country->getName($request->getLocale()));

        return $this->render('food/food_by_country.html.twig', [
            'pictures' => $pictures
        ]);
    }

    #[Route('/food/cuisine/{slugEn:cuisine}', name: 'food_by_cuisine')]
    public function foodByCuisine(Cuisine $cuisine, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByCuisine($cuisine);

        $this->addBreadcrumb('food.by_cuisine', 'food_index_by_cuisine');
        $this->addBreadcrumb($cuisine->getName($request->getLocale()));

        return $this->render('food/food_by_cuisine.html.twig', [
            'foods' => $foods
        ]);
    }

    #[Route('/food/tag/{slugEn:tag}', name: 'food_by_tag')]
    public function foodByFoodTag(FoodTag $tag, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByFoodTag($tag);

        $this->addBreadcrumb('food.by_type', 'food_index_by_tag');
        $this->addBreadcrumb($tag->getName($request->getLocale()));

        return $this->render('food/food_by_tag.html.twig', [
            'foods' => $foods
        ]);
    }

    #[Route('/food/ingredient/{slugEn:ingredient}', name: 'food_by_ingredient')]
    public function foodByIngredient(Ingredient $ingredient, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByIngredient($ingredient);

        $this->addBreadcrumb('food.by_ingredient', 'food_index_by_ingredient');
        $this->addBreadcrumb($ingredient->getName($request->getLocale()));

        return $this->render('food/food_by_ingredient.html.twig', [
            'foods' => $foods
        ]);
    }


    #[Route('/food/d/{slug}', name: 'food_show')]
    public function show(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food
        ]);
    }
}
