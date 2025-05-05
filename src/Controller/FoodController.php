<?php

namespace App\Controller;

use App\Entity\FoodItem;
use App\Repository\FoodItemRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodController extends BaseController
{
    #[Route('/food', name: 'food_index')]
    public function index(FoodItemRepository $foodItemRepository): Response
    {
        $foodItems = $foodItemRepository->findAll();
        
        return $this->render('food/index.html.twig', [
            'foodItems' => $foodItems
        ]);
    }

    #[Route('/food/{id:foodItem}', name: 'food_show')]
    public function show(FoodItem $foodItem): Response
    {
        return $this->render('food/show.html.twig', [
            'foodItem' => $foodItem
        ]);
    }
}
