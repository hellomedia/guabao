<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\FoodFixtures;
use App\DataFixtures\Tag\MediaTagFixtures;
use App\DataFixtures\Tag\PlaceTagFixtures;
use App\DataFixtures\TripFixtures;
use App\Entity\Food;
use App\Entity\Media;
use App\Entity\Tag\MediaTag;
use App\Entity\Tag\PlaceTag;
use App\Enum\MediaType;
use App\Helper\MediaAutoFillHelper;
use App\Pack\Media\Helper\ExifExtractor;
use App\Pack\Media\Helper\UploadHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Naming\OrignameNamer;

class FoodPictureFixtures extends Fixture implements DependentFixtureInterface
{
    private const FOOD_PICTURES = [
        [
            'filename' => 'belgian_tomato',
            'food' => FoodFixtures::TOMATO,
            'tags' => [MediaTagFixtures::HOME_COOKING],
            'isMeal' => false,
            'placeTags' => ['Liege']
        ],
        [
            'filename' => 'thai_muslim_1',
            'food' => FoodFixtures::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'tags' => [],
            'isMeal' => true,
            'placeTags' => ['Chiang Mai']
        ],
        [
            'filename' => 'thai_muslim_2',
            'food' => FoodFixtures::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'tags' => [],
            'isMeal' => true,
            'placeTags' => ['Chiang Mai']
        ],
    ];

    public function __construct(
        private MediaAutoFillHelper $autoFillHelper,
        private UploadHelper $uploadHelper,
        private ExifExtractor $exifExtractor,
    ) {}

    public function getDependencies(): array
    {
        return [
            PlaceTagFixtures::class,
            TripFixtures::class,
            FoodFixtures::class,
            TripCoverPictureFixtures::class, // force TripCoverMedia first because it removes old files
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::FOOD_PICTURES as $item) {

            $media = new Media();
            
            $media->setType(MediaType::IMAGE);
            
            $originalPath = __DIR__ . '/../image/food/' . $item['filename']. '.jpg';

            $uploadedFile = $this->uploadHelper->createUploadedFileForFixtures($originalPath);

            // extract exif before converting to avif (exif lost in conversion)
            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            $this->uploadHelper->uploadImage($media, $uploadedFile);

            $media->setIsMeal($item['isMeal']);
            
            foreach ($item['tags'] as $tag) {
                $media->addTag($this->getReference('mediaTag-' . $tag, MediaTag::class));
            }

            foreach ($item['placeTags'] as $tag) {
                $media->addPlaceTag($this->getReference('placeTag-' . $tag, PlaceTag::class));
            }

            $this->_updateAutoFields($media, $exif);

            $media->setFood($this->getReference('food-' . $item['food'], Food::class));

            $this->setReference('media-'. $item['filename'], $media);

            $manager->persist($media);

            $manager->flush();
        }
    }

    private function _updateAutoFields(Media $media, array|false $exif)
    {
        $this->autoFillHelper->_setTakenAt($media, $exif);

        $this->autoFillHelper->_setCoordinates($media, $exif);

        // currently no place fixtures, so nothing in the DB to link to,
        // but if we do add place fixtures
        // we could query the DB  as long as PlaceFixtures is added to the dependencies
        $this->autoFillHelper->_autoAssignPlace($media);

        $this->autoFillHelper->_setTrip($media);

        $this->autoFillHelper->_setMeal($media);
    }
}
