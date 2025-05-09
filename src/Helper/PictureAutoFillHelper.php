<?php 

namespace App\Helper;

use App\Entity\Meal;
use App\Entity\Picture;
use App\Repository\MealRepository;
use App\Repository\PlaceRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;

class PictureAutoFillHelper
{
    public function __construct(
        private GpsParsingHelper $gpsParsingHelper,
        private TripRepository $tripRepository,
        private PlaceRepository $placeRepository,
        private MealRepository $mealRepository,
        private GoogleMapsApiHelper $mapsApiHelper, 
        private EntityManager $entityManager,
    )
    { 
    }

    public function _extractExifData(Picture $picture): array|false
    {
        $file = $picture->getImageFile();

        if (!$file instanceof \SplFileInfo || !file_exists($file->getPathname())) {
            return false;
        }

        return @exif_read_data($file->getPathname());
    }

    public function _setTakenAt(Picture $picture, array|false $exif)
    {
        if ($picture->getTakenAt() !== null) {
            return; // already set manually
        }

        if (!empty($exif['DateTimeOriginal'])) {
            $date = \DateTimeImmutable::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
            if ($date) {
                $picture->setTakenAt($date);
            }
        }
    }

    public function _setTrip(Picture $picture)
    {
        if ($picture->getTrip() !== null) {
            return; // already set manually
        }

        $trip = $this->tripRepository->findOneByPictureDate($picture->getTakenAt());

        if ($trip) {
            $picture->setTrip($trip);
        }
    }

    public function _setCoordinates(Picture $picture, array|false $exif)
    {
        if (!empty($exif['GPSLatitude']) && !empty($exif['GPSLongitude'])) {
            $lat = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
            $lng = $this->gpsParsingHelper->getGpsDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

            $picture->setLatitude($lat);
            $picture->setLongitude($lng);
        }
    }

    public function _autoAssignPlace(Picture $picture): void
    {
        if ($picture->getPlace() !== null) {
            return; // already set manually
        }

        $lat = $picture->getLatitude();
        $lng = $picture->getLongitude();

        if ($lat === null || $lng === null) {
            return;
        }

        $nearby = $this->placeRepository->findNearby($lat, $lng);

        if ($nearby !== null) {
            $picture->setPlace($nearby);
        }
    }

    public function _setMeal(Picture $picture): void
    {
        // only if meal flag is true
        if ($picture->isMeal() == false) {
            return;
        }

        // only if meal not set 
        if ($picture->getMeal() !== null) {
            return; // already set
        }

        $meal = $this->mealRepository->findOneByPictureDate($picture->getTakenAt());

        if (!$meal) {
            $meal = new Meal;
            $meal->setEnjoyedAt($picture->getTakenAt());
            // meal place : will be set when it's set for a meal picture
            
            // place tag, let's auto-fill
            foreach ($picture->getPlaceTags() as $placeTag) {
                $meal->addPlaceTag($placeTag);
            }
           
            $this->entityManager->persist($meal);
        }

        $picture->setMeal($meal);
    }
}