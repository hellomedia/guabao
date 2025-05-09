<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Tag\FoodTag;
use App\Repository\FoodTagRepository;
use App\Repository\FoodRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodByTagController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
        $this->addBreadcrumb('food.index', 'food_index');
    }

    #[Route('/food/tag', name: 'food_by_tag_index')]
    public function index(FoodTagRepository $foodTagRepository): Response
    {
        $tags = $foodTagRepository->findAll();

        $this->addBreadcrumb('food.by_tag');

        return $this->render('food/tag/index.html.twig', [
            'tags' => $tags
        ]);
    }

    #[Route('/food/tag/{slugEn:tag}', name: 'food_by_tag_tag')]
    public function tag(FoodTag $tag, FoodRepository $foodRepository, Request $request): Response
    {
        $foods = $foodRepository->findByFoodTag($tag);

        $this->addBreadcrumb('food.by_tag', 'food_by_tag_index');
        $this->addBreadcrumb($tag->getName($request->getLocale()));

        return $this->render('food/tag/tag.html.twig', [
            'foods' => $foods,
            'tag' => $tag,
        ]);
    }

    #[Route('/food/tag/{slugTag}/{slugEn:food}', name: 'food_by_tag_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugTag)')] FoodTag $tag,
        Food $food,
        Request $request
        ): Response
    {
        $this->addBreadcrumb('food.by_tag', 'food_by_tag_index');
        $this->addBreadcrumb($tag->getName($request->getLocale()), 'food_by_tag_tag', ['slugEn' => $tag->getSlugEn()]);
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/food.html.twig', [
            'food' => $food,
            'meals' => $food->getMeals(),
        ]);
    }
}
