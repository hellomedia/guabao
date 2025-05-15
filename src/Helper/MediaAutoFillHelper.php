<?php 

namespace App\Helper;

use App\Entity\Meal;
use App\Entity\Media;
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

    public function _setTakenAt(Media $media, array|false $exif)
    {
        if ($media->getTakenAt() !== null) {
            return; // already set manually
        }

        if (!empty($exif['DateTimeOriginal'])) {
            $date = \DateTimeImmutable::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
            if ($date) {
                $media->setTakenAt($date);
            }
        }
    }

    public function _setTrip(Media $media)
    {
        if ($media->getTrip() !== null) {
            return; // already set manually
        }

        $trip = $this->tripRepository->findOneByMediaDate($media->getTakenAt());

        if ($trip) {
            $media->setTrip($trip);
        }
    }

    public function _setCoordinates(Media $media, array|false $exif)
    {
        if (!empty($exif['GPSLatitude']) && !empty($exif['GPSLongitude'])) {
            $lat = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
            $lng = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

            $media->setLatitude($lat);
            $media->setLongitude($lng);
        }
    }

    public function _autoAssignPlace(Media $media): void
    {
        if ($media->getPlace() !== null) {
            return; // already set manually
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

    public function _setMeal(Media $media): void
    {
        // only if meal flag is true
        if ($media->isMeal() == false) {
            return;
        }

        // only if meal not set 
        if ($media->getMeal() !== null) {
            return; // already set
        }

        $meal = $this->mealRepository->findOneByMediaDate($media->getTakenAt());

        if (!$meal) {
            $meal = new Meal;
            $meal->setEnjoyedAt($media->getTakenAt());
            // meal place : will be set when it's set for a meal media
            
            // place tag, let's auto-fill
            foreach ($media->getPlaceTags() as $placeTag) {
                $meal->addPlaceTag($placeTag);
            }
           
            $this->entityManager->persist($meal);
        }

        $media->setMeal($meal);
    }
}