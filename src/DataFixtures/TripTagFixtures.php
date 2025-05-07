<?php

namespace App\DataFixtures;

use App\Entity\Tag\TripTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TripTagFixtures extends Fixture
{
    public const SLOW_TRAVEL = 'slowtravel';
    public const HIKING = 'hiking';
    public const SOLO = 'solo';
    public const WITH_FRIENDS = 'friends';

    private const array TRIP_TAGS = [
        [
            'key' => self::SLOW_TRAVEL,
            'nameFr' => 'Slow travel',
            'nameEn' => 'Slow travel',
        ],
        [
            'key' => self::HIKING,
            'nameFr' => 'Rando',
            'nameEn' => 'Hiking trip',
        ],
        [
            'key' => self::SOLO,
            'nameFr' => 'Solo',
            'nameEn' => 'Solo',
        ],
        [   
            'key' => self::WITH_FRIENDS,
            'nameFr' => 'Entre amis',
            'nameEn' => 'With friends',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::TRIP_TAGS as $item) {
            
            $tripTag = new TripTag();

            $tripTag->setKey($item['key']);
            $tripTag->setNameFr($item['nameFr']);
            $tripTag->setNameEn($item['nameEn']);

            $this->setReference('triptag-' . $item['key'], $tripTag);
        
            $manager->persist($tripTag);
        }

        $manager->flush();
    }
}
