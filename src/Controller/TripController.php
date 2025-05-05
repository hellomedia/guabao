<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TripController extends BaseController
{
    public function preExecute()
    {
        $this->addBreadcrumb('homepage', 'homepage');
        $this->addBreadcrumb('trips', 'trip_index');
    }

    #[Route('/trip', name: 'trip_index')]
    public function index(TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findAll();
        
        return $this->render('trip/index.html.twig', [
            'trips' => $trips
        ]);
    }

    #[Route('/trip/{slug}', name: 'trip_show')]
    public function show(
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Trip $trip,
    ): Response
    {
        $this->addBreadcrumb($trip);

        return $this->render('trip/show.html.twig', [
            'trip' => $trip
        ]);
    }
}
