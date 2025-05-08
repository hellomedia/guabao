<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\CountryRepository;
use App\Repository\CuisineRepository;
use App\Repository\FoodRepository;
use App\Repository\FoodTagRepository;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
        $this->addBreadcrumb('food', 'food_index');
    }

    #[Route('/food', name: 'food_index')]
    public function index(FoodRepository $foodRepository): Response
    {
        $foods = $foodRepository->findAll();
        
        return $this->render('food/index.html.twig', [
            'foods' => $foods
        ]);
    }

    #[Route('/food/country', name: 'food_index_by_country')]
    public function indexByCountry(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findAll();
        
        return $this->render('food/index_by_country.html.twig', [
            'countries' => $countries
        ]);
    }

    #[Route('/food/cuisine', name: 'food_index_by_cuisine')]
    public function indexByCuisine(CuisineRepository $cuisineRepository): Response
    {
        $cuisines = $cuisineRepository->findAll();
        
        return $this->render('food/index_by_cuisine.html.twig', [
            'cuisines' => $cuisines
        ]);
    }

    #[Route('/food/tag', name: 'food_index_by_tag')]
    public function indexByTag(FoodTagRepository $foodTagRepository): Response
    {
        $tags = $foodTagRepository->findAll();
        
        return $this->render('food/index_by_tag.html.twig', [
            'tags' => $tags
        ]);
    }

    #[Route('/food/ingredient', name: 'food_index_by_ingredient')]
    public function indexByIngredient(IngredientRepository $ingredientRepository): Response
    {
        $ingredients = $ingredientRepository->findAll();
        
        return $this->render('food/index_by_ingredient.html.twig', [
            'ingredients' => $ingredients
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
