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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class TripCoverPictureFixtures extends Fixture implements DependentFixtureInterface
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
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->_removeOldFiles();
                
        foreach (TripFixtures::TRIPS as $trip) {

            $media = new Media();

            $media->setType(MediaType::IMAGE);
            
            $originalPath = __DIR__ . '/../image/trips/' . $trip['key'] . '/cover.jpg';

            $uploadedFile = $this->uploadHelper->createUploadedFileForFixtures($originalPath);

            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            $this->uploadHelper->uploadImage($media, $uploadedFile);

            $trip = $this->getReference($trip['key'], Trip::class);

            $media->setTrip($trip);

            $media->setIsTripCover(true);
            $trip->setCover($media);

            $this->_updateAutoFields($media, $exif);
    
            $manager->persist($media);
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

    private function _removeOldFiles()
    {
        $filesystem = new Filesystem();
        $uploadDir = __DIR__ . '/../../../public/uploads/media';

        // Delete all files in the upload directory (but not the directory itself)
        if ($filesystem->exists($uploadDir)) {
            $finder = new Finder();
            $finder->files()->in($uploadDir);

            foreach ($finder as $file) {
                $filesystem->remove($file->getRealPath());
            }
        }
    }
}
