<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\Trip;
use App\Helper\PictureAutoFillHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HighlightedPictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PictureAutoFillHelper $autoFillHelper
    )
    {   
    }

    public function getDependencies(): array
    {
        return [
            TripFixtures::class,
            CoverPictureFixtures::class, // force CoverPicture first because it removes old files
        ];
    }

    public function load(ObjectManager $manager): void
    {        
        foreach (TripFixtures::TRIPS as $trip) {

            $key = $trip['key'];

            $dir = __DIR__ . '/image/trip_highlights/' . $key . '/';

            foreach (glob($dir . '*.jpg') as $originalPath) {

                // Copy to temp
                $tempPath = sys_get_temp_dir() . '/' . uniqid('upload_', true) . '.jpg';
                copy($originalPath, $tempPath);

                $uploadedFile = new UploadedFile(
                    $tempPath,
                    basename($originalPath),
                    null,
                    null,
                    true
                );

                $picture = new Picture();

                $picture->setImageFile($uploadedFile);
                $picture->setUpdatedAt(new \DateTimeImmutable()); // Required by Vich to trigger update

                $trip = $this->getReference($key, Trip::class);
                $picture->setTrip($trip);
                $picture->setHighlight(true);

                $this->_updateAutoFields($picture);
        
                $manager->persist($picture);
            }
        }

        $manager->flush();
    }

    private function _updateAutoFields(Picture $picture)
    {
        $exif = $this->autoFillHelper->_extractExifData($picture);

        $this->autoFillHelper->_setTakenAt($picture, $exif);

        $this->autoFillHelper->_setCoordinates($picture, $exif);

        // currently no place fixtures, so nothing in the DB to link to,
        // but if we do add place fixtures
        // we could query the DB  as long as PlaceFixtures is added to the dependencies
        $this->autoFillHelper->_autoAssignPlace($picture);
    }
}
