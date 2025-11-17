<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\CuisineRepository;
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
    public function index(CuisineRepository $cuisineRepository): Response
    {
        $cuisines = $cuisineRepository->findAll();

        $this->addBreadcrumb('food.by_cuisine');
        
        return $this->render('food/cuisine/index.html.twig', [
            'cuisines' => $cuisines
        ]);
    }

    #[Route('/food/d/{slug}', name: 'food_show')]
    public function show(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] Food $food, Request $request): Response
    {
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/show.html.twig', [
            'food' => $food
        ]);
    }
}
