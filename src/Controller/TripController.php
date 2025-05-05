<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TripController extends BaseController
{
    #[Route('/trip', name: 'trip_index')]
    public function index(TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findAll();

        foreach ($trips as $trip) {
            $placeTags = $tripRepository->findPlaceTags($trip);
            $cover = $tripRepository->findCover($trip);
            $trip->setPlaceTags($placeTags);
            $trip->setCover($cover);
            dump($cover);
        }
        
        return $this->render('trip/index.html.twig', [
            'trips' => $trips
        ]);
    }

    #[Route('/trip/{id:trip}', name: 'trip_show')]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip
        ]);
    }
}
