<?php

namespace App\Controller;

use App\Entity\Cuisine;
use App\Entity\Food;
use App\Repository\CuisineRepository;
use App\Repository\FoodRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodByCuisineController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
    }

    #[Route('/food/cuisine', name: 'food_by_cuisine_index')]
    public function index(CuisineRepository $cuisineRepository): Response
    {
        $cuisines = $cuisineRepository->findAll();

        $this->addBreadcrumb('food.by_cuisine');

        return $this->render('food/cuisine/index.html.twig', [
            'cuisines' => $cuisines
        ]);
    }

    #[Route('/food/cuisine/{slugEn:cuisine}', name: 'food_by_cuisine_cuisine')]
    public function cuisine(Cuisine $cuisine, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByCuisine($cuisine);

        $this->addBreadcrumb('food.by_cuisine', 'food_by_cuisine_index');
        $this->addBreadcrumb($cuisine->getName($request->getLocale()));

        return $this->render('food/cuisine/cuisine.html.twig', [
            'foods' => $foods,
            'cuisine' => $cuisine,
        ]);
    }

    #[Route('/food/cuisine/{slugCuisine}/{slugEn:food}', name: 'food_by_cuisine_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugCuisine)')] Cuisine $cuisine,
        Food $food,
        Request $request
        ): Response
    {
        $this->addBreadcrumb('food.by_cuisine', 'food_by_cuisine_index');
        $this->addBreadcrumb(
            $cuisine->getName($request->getLocale()), 'food_by_cuisine_cuisine',
            ['slugEn' => $cuisine->getSlugEn()],
            isLarge: true
        );
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/food.html.twig', [
            'food' => $food,
            'meals' => $food->getMeals(),
        ]);
    }
}
