<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use App\Repository\StoryRepository;
use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends BaseController
{
    #[Route('/', name: 'homepage')]
    public function homepage(TripRepository $tripRepository, StoryRepository $storyRepository, FoodRepository $foodRepository): Response
    {
        $this->addBreadcrumb('homepage', 'homepage');

        $topSlowTravelTrips = $tripRepository->findTopSlowTravelTrips();
        $topHikingTrips = $tripRepository->findTopHikingTrips();
        $topAdventures = $storyRepository->findTopAdventures();
        $topDiscoveries = $storyRepository->findTopDiscoveries();
        $topHistoricalPlaces = $storyRepository->findTopHistoricalPlaces();
        $topPersonalExperiences = $storyRepository->findTopPersonalExperiences();
        $topFoodDiscoveries = $foodRepository->findTopDiscoveries();
        
        return $this->render('default/homepage.html.twig', [
            'top_slow_travel_trips' => $topSlowTravelTrips,
            'top_hiking_trips' => $topHikingTrips,
            'top_adventures' => $topAdventures,
            'top_discoveries' => $topDiscoveries,
            'top_historical_places' => $topHistoricalPlaces,
            'top_personal_experiences' => $topPersonalExperiences,
            'top_food_discoveries' => $topFoodDiscoveries,
        ]);
    }
}
