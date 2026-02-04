<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends BaseController
{
    #[Route('/', name: 'homepage')]
    public function homepage(TripRepository $tripRepository): Response
    {
        $this->addBreadcrumb('homepage', 'homepage');

        $topSlowTravelTrips = $tripRepository->findTopSlowTravelTrips();
        $topHikingTrips = $tripRepository->findTopHikingTrips();
        
        return $this->render('default/homepage.html.twig', [
            'top_slow_travel_trips' => $topSlowTravelTrips,
            'top_hiking_trips' => $topHikingTrips,
        ]);
    }
}
