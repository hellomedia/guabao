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
        $cuisinesWithCounts = $cuisineRepository->findAllWithFoodCount();

        $this->addBreadcrumb('food.by_cuisine');

        return $this->render('food/cuisine/index.html.twig', [
            'cuisines_with_counts' => $cuisinesWithCounts
        ]);
    }

    #[Route('/food/cuisine/{slugEn:cuisine}', name: 'food_by_cuisine_cuisine')]
    public function cuisine(Cuisine $cuisine, FoodRepository $foodRepository, Request $request): Response
    {
        $foodList = $foodRepository->findByCuisine($cuisine);

        $this->addBreadcrumb('food.by_cuisine', 'food_by_cuisine_index');
        $this->addBreadcrumb($cuisine->getName($request->getLocale()));

        return $this->render('food/cuisine/cuisine.html.twig', [
            'foodList' => $foodList,
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

        return $this->render('food/show.html.twig', [
            'food' => $food,
            'media_groups' => $food->getMediaGroups(),
        ]);
    }
}
