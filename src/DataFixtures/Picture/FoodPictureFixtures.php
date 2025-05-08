<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\FoodFixtures;
use App\DataFixtures\Tag\PictureTagFixtures;
use App\DataFixtures\TripFixtures;
use App\Entity\Food;
use App\Entity\Picture;
use App\Entity\Tag\PictureTag;
use App\Helper\PictureAutoFillHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FoodPictureFixtures extends Fixture implements DependentFixtureInterface
{
    private const FOOD_PICTURES = [
        [
            'filename' => 'belgian_tomato',
            'food' => FoodFixtures::TOMATO,
            'tags' => [PictureTagFixtures::HOME_COOKING],
            'isMeal' => false,
        ],
        [
            'filename' => 'thai_muslim_1',
            'food' => FoodFixtures::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'tags' => [],
            'isMeal' => true,
        ],
        [
            'filename' => 'thai_muslim_2',
            'food' => FoodFixtures::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'tags' => [],
            'isMeal' => true,
        ],
    ];

    public function __construct(
        private PictureAutoFillHelper $autoFillHelper,
    ) {}

    public function getDependencies(): array
    {
        return [
            TripFixtures::class,
            FoodFixtures::class,
            TripCoverPictureFixtures::class, // force TripCoverPicture first because it removes old files
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::FOOD_PICTURES as $item) {

            $originalPath = __DIR__ . '/../image/food/' . $item['filename']. '.jpg';

            // Copy to a temporary file (unique filename each time)
            // so that original file is not moved by upload process and still avaialable for next time
            $tempPath = sys_get_temp_dir() . '/' . uniqid('upload_', true) . '.jpg';
            copy($originalPath, $tempPath);

            $uploadedFile = new UploadedFile(
                $tempPath,
                $item['filename'] . '.jpg',
                null,
                null,
                true // true = test mode, skips file upload checks
            );

            $picture = new Picture;

            $picture->setImageFile($uploadedFile);
            $picture->setUpdatedAt(new \DateTimeImmutable()); // Required by Vich to trigger update

            $picture->setIsMeal($item['isMeal']);

            $this->_updateAutoFields($picture);

            $picture->setFood($this->getReference('food-' . $item['food'], Food::class));

            foreach ($item['tags'] as $tag) {
                $picture->addTag($this->getReference('pictureTag-' . $tag, PictureTag::class));
            }

            $this->setReference('picture-'. $item['filename'], $picture);

            $manager->persist($picture);

            // flush each item 
            // new meals need to be flushed to be detectable for following items
            $manager->flush();
        }
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

        $this->autoFillHelper->_setTrip($picture);

        $this->autoFillHelper->_setMeal($picture);
    }
}
