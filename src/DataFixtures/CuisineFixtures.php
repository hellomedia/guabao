<?php

namespace App\DataFixtures;

use App\Entity\Cuisine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CuisineFixtures extends Fixture
{
    public const BELGIAN = 'belgian';
    public const FRENCH = 'french';
    public const ITALIAN = 'italian';
    public const AMERICAN = 'american';
    public const TEXMEX = 'texmex';
    public const CHINESE = 'chinese';
    public const KOREAN = 'korean';
    public const VIETNAMESE = 'vietnamese';
    public const TAIWANESE = 'taiwanese';
    public const HONGKONG = 'hongkong';
    public const THAI = 'thai';
    public const MALAYSIAN = 'malaisian';
    public const HOKIEN = 'hokien';
    public const INDIAN = 'indian';
    public const INDONESIAN = 'indonesian';
    public const FILIPINO = 'filipino';

    private const array CUISINES = [
        [
            'key' => self::BELGIAN,
            'nameFr' => 'Belge',
            'nameEn' => 'Belgian',
        ],
        [
            'key' => self::FRENCH,
            'nameFr' => 'Française',
            'nameEn' => 'French',
        ],
        [
            'key' => self::ITALIAN,
            'nameFr' => 'Italienne',
            'nameEn' => 'Italian',
        ],
        [
            'key' => self::AMERICAN,
            'nameFr' => 'Américaine',
            'nameEn' => 'American',
        ],
        [
            'key' => self::TEXMEX,
            'nameFr' => 'Tex-Mex',
            'nameEn' => 'Tex-Mex',
        ],
        [
            'key' => self::CHINESE,
            'nameFr' => 'Chinoise',
            'nameEn' => 'Chinese',
        ],
        [
            'key' => self::KOREAN,
            'nameFr' => 'Coréenne',
            'nameEn' => 'Korean',
        ],
        [
            'key' => self::VIETNAMESE,
            'nameFr' => 'Vietnamienne',
            'nameEn' => 'Vietnamese',
        ],
        [
            'key' => self::TAIWANESE,
            'nameFr' => 'Taiwanese',
            'nameEn' => 'Taiwanese',
        ],
        [
            'key' => self::HONGKONG,
            'nameFr' => 'Hong Kong',
            'nameEn' => 'Hong Kong',
        ],
        [
            'key' => self::THAI,
            'nameFr' => 'Thai',
            'nameEn' => 'Thai',
        ],
        [
            'key' => self::MALAYSIAN,
            'nameFr' => 'Malaisienne',
            'nameEn' => 'Malay',
        ],
        [
            'key' => self::HOKIEN,
            'nameFr' => 'Hokien',
            'nameEn' => 'Hokien',
        ],
        [
            'key' => self::INDONESIAN,
            'nameFr' => 'Indonésienne',
            'nameEn' => 'Indonesian',
        ],
        [
            'key' => self::FILIPINO,
            'nameFr' => 'Filipino',
            'nameEn' => 'Filipino',
        ],
        [
            'key' => self::INDIAN,
            'nameFr' => 'Indienne',
            'nameEn' => 'Indian',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CUISINES as $item) {
            $cuisine = new Cuisine();

            $cuisine->setNameFr($item['nameFr']);
            $cuisine->setNameEn($item['nameEn']);

            $this->setReference('cuisine-' . $item['key'], $cuisine);

            $manager->persist($cuisine);
        }

        $manager->flush();
    }
}
