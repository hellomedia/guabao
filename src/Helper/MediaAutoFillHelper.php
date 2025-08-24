<?php 

namespace App\Helper;

use App\Entity\Meal;
use App\Entity\Media;
use App\Entity\Trip;
use App\Repository\MealRepository;
use App\Repository\PlaceRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;

class MediaAutoFillHelper
{
    public function __construct(
        private GpsParsingHelper $gpsParsingHelper,
        private TripRepository $tripRepository,
        private PlaceRepository $placeRepository,
        private MealRepository $mealRepository,
        private GoogleMapsApiHelper $mapsApiHelper, 
        private EntityManager $entityManager,
        private string $uploadsPath,
    )
    { 
    }

    public function setTakenAt(Media $media, array|false $exif)
    {
        if ($media->getTakenAt() !== null) {
            return; // already set
        }

        // avoid dealing with timezones here. It creates the following issue in easyadmin:
        // Date is handled as UTC in js date widget, but interpreted as php/server timezone by php.
        // So when the form is submitted, even if the date was not changed, it is modified by
        // the time difference between UTC and php/server timezone.

        if (!empty($exif['DateTimeOriginal'])) {
            $date = \DateTimeImmutable::createFromFormat(
                'Y:m:d H:i:s',
                $exif['DateTimeOriginal'],            
            );

            if ($date !== false) {
                $media->setTakenAt($date);
            }
        }   
    }

    public function autoAssignTrip(Media $media)
    {
        if ($media->getTrip() !== null) {
            return; // already set
        }

        $trip = $this->tripRepository->findOneByMediaDate($media->getTakenAt());

        \assert($trip instanceof Trip);

        if ($trip) {
            $media->setTrip($trip);
        }
    }

    public function setCoordinates(Media $media, array|false $exif)
    {
        if ($media->getLatitude() !== null) {
            return; // already set
        }

        if (!empty($exif['GPSLatitude']) && !empty($exif['GPSLongitude'])) {
            $lat = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
            $lng = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

            $media->setLatitude($lat);
            $media->setLongitude($lng);
        }
    }

    public function autoAssignPlace(Media $media): void
    {
        if ($media->getPlace() !== null) {
            return; // already set
        }

        $lat = $media->getLatitude();
        $lng = $media->getLongitude();

        if ($lat === null || $lng === null) {
            return;
        }

        $nearby = $this->placeRepository->findNearby($lat, $lng);

        if ($nearby !== null) {
            $media->setPlace($nearby);
        }
    }

    public function autoAssignMeal(Media $media): void
    {
        if ($media->getMeal() !== null) {
            return; // already set
        }

        if ($media->isMeal() == false) {
            return;
        }

        $meal = $this->mealRepository->findOneByMediaDate($media->getTakenAt());

        if (!$meal) {
            $meal = $this->_autoCreateMeal($media);
        }

        $media->setMeal($meal);
    }

    private function _autoCreateMeal(Media $media): Meal
    {
        $meal = new Meal;

        $meal->setEnjoyedAt($media->getTakenAt());

        if ($media->getPlace() == null) {
            $this->autoAssignPlace($media);
        }

        if ($media->getPlace()) {
            $meal->setPlace($media->getPlace());
        }

        foreach ($media->getPlaceTags() as $placeTag) {
            $meal->addPlaceTag($placeTag);
        }

        $this->entityManager->persist($meal);

        return $meal;
    }

    public function suggestPlace(Media $media): ?string
    {
        if ($media->getPlace() != null) {
            return null;
        }
    
        $lat = $media->getLatitude();
        $lng = $media->getLongitude();

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