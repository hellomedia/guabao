<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\TripFixtures;
use App\Entity\Media;
use App\Entity\Trip;
use App\Enum\MediaType;
use App\Helper\MediaAutoFillHelper;
use App\Pack\Media\Helper\ExifExtractor;
use App\Pack\Media\Helper\UploadHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PanoFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private MediaAutoFillHelper $autoFillHelper,
        private UploadHelper $uploadHelper,
        private ExifExtractor $exifExtractor,
    )
    {   
    }

    public function getDependencies(): array
    {
        return [
            TripFixtures::class,
            TripCoverPictureFixtures::class, // force TripCoverMedia first because it removes old files
        ];
    }

    public function load(ObjectManager $manager): void
    {        
        foreach (TripFixtures::TRIPS as $trip) {

            $key = $trip['key'];

            $dir = __DIR__ . '/../image/trips/' . $key . '/pano/';

            foreach (glob($dir . '*.jpg') as $originalPath) {

                $media = new Media();
                
                $media->setType(MediaType::IMAGE);
                $media->setIsPano(true);
                
                $uploadedFile = $this->uploadHelper->createUploadedFileForFixtures($originalPath);
                
                // extract exif before converting to avif (exif lost in conversion)
                $exif = $this->exifExtractor->extractExifData($uploadedFile);

                $this->uploadHelper->uploadImage($media, $uploadedFile);

                $trip = $this->getReference($key, Trip::class);
                
                $media->setTrip($trip);
                
                $this->_updateAutoFields($media, $exif);
        
                $manager->persist($media);
            }
        }

        $manager->flush();
    }

    private function _updateAutoFields(Media $media, array|false $exif)
    {
        $this->autoFillHelper->_setTakenAt($media, $exif);

        $this->autoFillHelper->_setCoordinates($media, $exif);

        // currently no place fixtures, so nothing in the DB to link to,
        // but if we do add place fixtures
        // we could query the DB  as long as PlaceFixtures is added to the dependencies
        $this->autoFillHelper->_autoAssignPlace($media);
    }
}
