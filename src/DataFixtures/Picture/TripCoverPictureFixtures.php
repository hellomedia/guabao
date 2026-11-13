<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\TripFixtures;
use App\Entity\Media;
use App\Entity\Trip;
use App\Enum\MediaType;
use App\Helper\MediaAutoFillHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Pack\Media\Helper\ExifExtractor;
use Pack\Media\Helper\UploadHelper;
use Symfony\Component\Filesystem\Filesystem;

class TripCoverPictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private MediaAutoFillHelper $autoFillHelper,
        private UploadHelper $uploadHelper,
        private ExifExtractor $exifExtractor,
        private string $uploadsPath,
    ) {}

    public function getDependencies(): array
    {
        return [
            TripFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // The purge has just emptied the media table: drop the files it orphaned.
        // %uploads_path% is public/uploads in dev, var/test/uploads in test; the bundle is dev/test only.
        // Runs first: the other picture fixtures depend on this one.
        (new Filesystem())->remove($this->uploadsPath . '/' . UploadHelper::IMAGE_DIR);

        foreach (TripFixtures::TRIPS as $trip) {
            $media = new Media();

            $media->setType(MediaType::IMAGE);

            $originalPath = __DIR__ . '/../images/trips/' . $trip['key'] . '/cover.jpg';

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
        $this->autoFillHelper->setTakenAt($media, $exif);

        $this->autoFillHelper->setCoordinates($media, $exif);

        // currently no place fixtures, so nothing in the DB to link to,
        // but if we do add place fixtures
        // we could query the DB  as long as PlaceFixtures is added to the dependencies
        $this->autoFillHelper->autoAssignPlace($media);
    }
}
