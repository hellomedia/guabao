<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodController extends BaseController
{
    #[Route('/food', name: 'food_index')]
    public function index(FoodRepository $FoodRepository): Response
    {
        $Foods = $FoodRepository->findAll();
        
        return $this->render('food/index.html.twig', [
            'Foods' => $Foods
        ]);
    }

    #[Route('/food/{id:Food}', name: 'food_show')]
    public function show(Food $Food): Response
    {
        return $this->render('food/show.html.twig', [
            'Food' => $Food
        ]);
    }
}
