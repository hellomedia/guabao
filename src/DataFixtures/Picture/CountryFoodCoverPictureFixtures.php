<?php

namespace App\DataFixtures\Picture;

use App\DataFixtures\TripFixtures;
use App\Helper\PictureAutoFillHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CountryFoodCoverPictureFixtures extends Fixture implements DependentFixtureInterface
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
            TripCoverPictureFixtures::class, // force TripCoverPicture first because it removes old files
            FoodPictureFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {     

       
    }

}
