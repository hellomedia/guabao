<?php 

namespace App\Helper;

use App\Entity\Meal;
use App\Repository\MealRepository;
use App\Repository\PlaceRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;

class MealAutoFillHelper
{
    public function __construct(
        private TripRepository $tripRepository,
        private PlaceRepository $placeRepository,
        private MealRepository $mealRepository,
        private GoogleMapsApiHelper $mapsApiHelper, 
        private EntityManager $entityManager,
    )
    { 
    }

    public function autoAssignPlace(Meal $meal): void
    {
        if ($meal->getPlace() !== null) {
            return; // already set
        }

        if ($meal->getMedias()->isEmpty()) {
            return;
        }

        $firstMedia = $meal->getMedias()->first();
        $lat = $firstMedia->getLatitude();
        $lng = $firstMedia->getLongitude();

        if ($lat === null || $lng === null) {
            return;
        }

        $nearby = $this->placeRepository->findNearby($lat, $lng);

        if ($nearby !== null) {
            $meal->setPlace($nearby);
        }
    }

    public function suggestPlace(Meal $meal): ?string
    {
        if ($meal->getPlace() !== null) {
            return null;
        }

        if ($meal->getMedias()->isEmpty()) {
            return null;
        }

        $firstMedia = $meal->getMedias()->first();
        $lat = $firstMedia->getLatitude();
        $lng = $firstMedia->getLongitude();

        // No nearby match, suggest new place
        // Suggestion only — don't persist
        $suggestion = $this->mapsApiHelper->findNearbyPlace($lat, $lng);

        if ($suggestion == null) {
            return null;
        }

        $name = $suggestion['name'] ?? 'Unknown';
        $address = $suggestion['vicinity'] ?? 'Unknown address';
        $placeId = $suggestion['place_id'] ?? null;
        $location = $suggestion['geometry']['location'] ?? [];

        $placeUrl = $placeId
            ? 'https://www.google.com/maps/place/?q=place_id=' . urlencode($placeId)
            : null;

        $coordUrl = isset($location['lat'], $location['lng'])
            ? sprintf('https://www.google.com/maps/search/?api=1&query=%f,%f', $location['lat'], $location['lng'])
            : null;

        $linkParts = [];
        if ($placeUrl) {
            $linkParts[] = sprintf('<a href="%s" target="_blank">Google Maps (place)</a>', $placeUrl);
        }
        if ($coordUrl) {
            $linkParts[] = sprintf('<a href="%s" target="_blank">By coordinates</a>', $coordUrl);
        }

        return sprintf(
            '📍 Suggested place: <strong>%s</strong><br><small>%s</small><br>%s',
            htmlspecialchars($name),
            htmlspecialchars($address),
            implode(' | ', $linkParts)
        );
    }
}