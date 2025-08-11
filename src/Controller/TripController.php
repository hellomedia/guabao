<?php

namespace App\Controller;

use App\DataFixtures\Tag\TripTagFixtures;
use App\Entity\Trip;
use App\Repository\MediaRepository;
use App\Repository\TripRepository;
use App\Repository\TripTagRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(TripRepository $tripRepository, Request $request): Response
    {
        $trips = $tripRepository->findAll(sort: $request->query->get('s'));
        
        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
        ]);
    }

    #[Route('/trip/t/hiking', name: 'trip_index_hiking')]
    public function indexByHiking(TripRepository $tripRepository, TripTagRepository $tripTagRepository, Request $request): Response
    {
        $hikingTripTag = $tripTagRepository->findOneByKey(TripTagFixtures::HIKING);

        $trips = $tripRepository->findAllByTag($hikingTripTag, sort: $request->query->get('s'));

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
        ]);
    }

    #[Route('/trip/t/slow-travel', name: 'trip_index_slow_travel')]
    public function indexBySlowTravel(TripRepository $tripRepository, TripTagRepository $tripTagRepository, Request $request): Response
    {
        $slowTravelTripTag = $tripTagRepository->findOneByKey(TripTagFixtures::SLOW_TRAVEL);

        $trips = $tripRepository->findAllByTag($slowTravelTripTag, sort: $request->query->get('s'));

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
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

    #[Route('/trip/{slug}/gallery', name: 'trip_gallery')]
    public function gallery(
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Trip $trip,
        MediaRepository $mediaRepository,
    ): Response {
        $this->addBreadcrumb($trip, isLarge: true);
        $this->addBreadcrumb('trip.gallery');

        $medias = $mediaRepository->findByTrip($trip);

        return $this->render('trip/gallery.html.twig', [
            'trip' => $trip,
            'medias' => $medias,
        ]);
    }
    
    #[Route('/trip/{parent}/{trip}', name: 'child_trip_show')]
    public function showChildTrip(
        #[MapEntity(expr: 'repository.findOneBySlug(parent)')] Trip $parent,
        #[MapEntity(expr: 'repository.findOneBySlug(trip)')] Trip $trip,
        MediaRepository $mediaRepository,
    ): Response
    {
        $this->addBreadcrumb($parent);
        // no need for this one since we have the top links serving as title
        // $this->addBreadcrumb($trip);

        $medias = $mediaRepository->findByTrip($trip);

        return $this->render('trip/show_child_trip.html.twig', [
            'trip' => $trip,
            'medias' => $medias,
        ]);
    }


}
