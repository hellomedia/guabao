<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\TripFixtures;
use App\Helper\MediaAutoFillHelper;
use App\Pack\Media\Helper\ExifExtractor;
use App\Pack\Media\Helper\UploadHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CountryFoodCoverPictureFixtures extends Fixture implements DependentFixtureInterface
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
            FoodPictureFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {     

       
    }

}
