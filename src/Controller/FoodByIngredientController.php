<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Ingredient;
use App\Repository\FoodRepository;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodByIngredientController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
        $this->addBreadcrumb('food.index', 'food_index');
    }

    #[Route('/food/ingredient', name: 'food_by_ingredient_index')]
    public function index(IngredientRepository $ingredientRepository): Response
    {
        $favourites = $ingredientRepository->findFavourites();
        $others = $ingredientRepository->findNonFavourites();

        $this->addBreadcrumb('food.by_ingredient');

        return $this->render('food/ingredient/index.html.twig', [
            'favourites' => $favourites,
            'others' => $others,        ]);
    }

    #[Route('/food/ingredient/{slugEn:ingredient}', name: 'food_by_ingredient_ingredient')]
    public function ingredient(Ingredient $ingredient, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByIngredient($ingredient);

        $this->addBreadcrumb('food.by_ingredient', 'food_by_ingredient_index');
        $this->addBreadcrumb($ingredient->getName($request->getLocale()));

        return $this->render('food/ingredient/ingredient.html.twig', [
            'foods' => $foods,
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/food/ingredient/{slugIngredient}/{slugEn:food}', name: 'food_by_ingredient_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugIngredient)')] Ingredient $ingredient,
        Food $food,
        Request $request
        ): Response
    {
        $this->addBreadcrumb('food.by_ingredient', 'food_by_cuisine_index');
        $this->addBreadcrumb($ingredient->getName($request->getLocale()), 'food_by_ingredient_ingredient', ['slugEn' => $ingredient->getSlugEn()]);
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/food.html.twig', [
            'food' => $food,
            'meals' => $food->getMeals(),
        ]);
    }
}
