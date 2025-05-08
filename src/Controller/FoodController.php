<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodController extends BaseController
{
    #[Route('/food', name: 'food_index')]
    public function index(FoodRepository $foodRepository): Response
    {
        $foods = $foodRepository->findAll();
        
        return $this->render('food/index.html.twig', [
            'foods' => $foods
        ]);
    }

    #[Route('/food/{slug}', name: 'food_show')]
    public function show(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food
        ]);
    }
}
