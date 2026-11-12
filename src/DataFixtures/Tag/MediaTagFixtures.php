<?php

namespace App\DataFixtures\Tag;

use App\Entity\Tag\MediaTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MediaTagFixtures extends Fixture
{
    public const BIVOUAC = 'bivouac';
    public const HIKING = 'hiking';
    public const SELF_SUPPORTED_HIKE = 'selfsupported';
    public const WORKING_PLACE = 'workingplace';
    public const LIBRARY = 'library';
    public const STRANGERS = 'strangers';
    public const KIDS = 'kids';
    public const HOME_COOKING = 'homecooking';
    public const FOOD_COURT = 'foodcourt';
    public const FUNKY = 'funky';
    public const COLOR = 'color';

    private const array PICTURE_TAGS = [
        [
            'key' => self::BIVOUAC,
            'nameFr' => 'Bivouac',
            'nameEn' => 'Hiking/trek camp',
        ],
        [
            'key' => self::HIKING,
            'nameFr' => 'Rando',
            'nameEn' => 'Hiking/trek',
        ],
        [
            'key' => self::SELF_SUPPORTED_HIKE,
            'nameFr' => 'Rando en autonomie',
            'nameEn' => 'Self-supported hike/trek',
        ],
        [
            'key' => self::WORKING_PLACE,
            'nameFr' => 'Lieu de travail',
            'nameEn' => 'Working place',
        ],
        [
            'key' => self::LIBRARY,
            'nameFr' => 'Bibli',
            'nameEn' => 'Libray',
        ],
        [
            'key' => self::STRANGERS,
            'nameFr' => 'Belles rencontres',
            'nameEn' => 'Memorable strangers',
        ],
        [
            'key' => self::KIDS,
            'nameFr' => 'Enfants',
            'nameEn' => 'Kids',
        ],
        [
            'key' => self::HOME_COOKING,
            'nameFr' => 'Home cooking',
            'nameEn' => 'Home cooking',
        ],
        [
            'key' => self::FOOD_COURT,
            'nameFr' => 'Food court',
            'nameEn' => 'Food court',
        ],
        [
            'key' => self::FUNKY,
            'nameFr' => 'Funky',
            'nameEn' => 'Funky',
        ],
        [
            'key' => self::COLOR,
            'nameFr' => 'Couleurs',
            'nameEn' => 'Color',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PICTURE_TAGS as $item) {
            $tag = new MediaTag();

            $tag->setNameFr($item['nameFr']);
            $tag->setNameEn($item['nameEn']);

            $this->setReference('mediaTag-' . $item['key'], $tag);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
