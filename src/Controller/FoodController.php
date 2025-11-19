<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
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
    public function index(FoodRepository $repository): Response
    {
        $foodList = $repository->findAll();

        $this->addBreadcrumb('food.all');

        return $this->render('food/all/index.html.twig', [
            'food_list' => $foodList
        ]);
    }

    #[Route('/food/d/{slug}', name: 'food_show')]
    public function show(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] Food $food, Request $request): Response
    {
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/show.html.twig', [
            'media_groups' => $food->getMediaGroups(),
        ]);
    }
}
