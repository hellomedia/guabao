<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\TripFixtures;
use App\Entity\Picture;
use App\Entity\Trip;
use App\Helper\PictureAutoFillHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TripCoverPictureFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private PictureAutoFillHelper $autoFillHelper,
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

            $key = $trip['key'];

            $originalPath = __DIR__ . '/../image/trips/' . $key . '/cover.jpg';

            // Copy to a temporary file (unique filename each time)
            // so that original file is not moved by upload process and still avaialable for next time
            $tempPath = sys_get_temp_dir() . '/' . uniqid('upload_', true) . '.jpg';
            copy($originalPath, $tempPath);

            $uploadedFile = new UploadedFile(
                $tempPath,
                'cover.jpg',
                null,
                null,
                true // true = test mode, skips file upload checks
            );

            $picture = new Picture();

            $picture->setImageFile($uploadedFile);
            $picture->setUpdatedAt(new \DateTimeImmutable()); // Required by Vich to trigger update

            $trip = $this->getReference($key, Trip::class);

            $picture->setTrip($trip);

            $trip->setCover($picture);

            $this->_updateAutoFields($picture);
    
            $manager->persist($picture);
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

    private function _removeOldFiles()
    {
        $filesystem = new Filesystem();
        $uploadDir = __DIR__ . '/../../../public/uploads/pictures';

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
