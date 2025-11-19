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

    #[Route('/food/tag', name: 'food_tags_index')]
    public function index(FoodTagRepository $foodTagRepository): Response
    {
        $tagsWithCounts = $foodTagRepository->findAllWithFoodCount();

        $this->addBreadcrumb('food.tags');

        return $this->render('food/tag/index.html.twig', [
            'tags_with_counts' => $tagsWithCounts
        ]);
    }

    #[Route('/food/tag/{slugEn:tag}', name: 'food_tags_tag')]
    public function tag(FoodTag $tag, FoodRepository $foodRepository, Request $request): Response
    {
        $foodList = $foodRepository->findByFoodTag($tag);

        $this->addBreadcrumb('food.tags', 'food_tags_index');
        $this->addBreadcrumb($tag->getName($request->getLocale()));

        return $this->render('food/tag/tag.html.twig', [
            'foodList' => $foodList,
            'tag' => $tag,
        ]);
    }

    #[Route('/food/tag/{slugTag}/{slugEn:food}', name: 'food_tags_food')]
    public function food(
        #[MapEntity(expr: 'repository.findOneBySlugEn(slugTag)')] FoodTag $tag,
        Food $food,
        Request $request
        ): Response
    {
        $this->addBreadcrumb('food.tags', 'food_tags_index');
        $this->addBreadcrumb(
            $tag->getName($request->getLocale()),
            'food_tags_tag', ['slugEn' => $tag->getSlugEn()],
            isLarge: true
        );
        $this->addBreadcrumb($food->getName($request->getLocale()));

        return $this->render('food/show.html.twig', [
            'food' => $food,
            'media_groups' => $food->getMediaGroups(),
        ]);
    }
}
