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

    #[Route('/food/ingredient', name: 'food_ingredients_index')]
    public function index(IngredientRepository $ingredientRepository): Response
    {
        $ingredients = $ingredientRepository->findAllByTypeWithFoodCount();

        $this->addBreadcrumb('food.ingredients');

        return $this->render('food/ingredient/index.html.twig', [
            'food_types' => $ingredients,
        ]);
    }

    #[Route('/food/ingredient/{slugEn:ingredient}', name: 'food_ingredients_ingredient')]
    public function ingredient(Ingredient $ingredient, FoodRepository $foodRepository, Request $request): Response
    {
        $foodList = $foodRepository->findByIngredient($ingredient);

        $this->addBreadcrumb('food.ingredients', 'food_ingredients_index');
        $this->addBreadcrumb($ingredient->getName($request->getLocale()));

        return $this->render('food/ingredient/ingredient.html.twig', [
            'foodList' => $foodList,
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/food/ingredient/{slugIngredient}/{slugEn:food}', name: 'food_ingredients_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugIngredient)')] Ingredient $ingredient,
        Food $food,
        Request $request
        ): Response
    {
        $this->addBreadcrumb('food.ingredients', 'food_cuisines_index');
        $this->addBreadcrumb(
            $ingredient->getName($request->getLocale()),
            'food_ingredients_ingredient', ['slugEn' => $ingredient->getSlugEn()],
            isLarge: true
        );
        //$this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/show.html.twig', [
            'food' => $food,
            'media_groups' => $food->getMediaGroups(),
        ]);
    }
}
