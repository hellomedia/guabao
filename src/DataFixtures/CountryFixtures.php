<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    private const array COUNTRIES = [
        [
            'code' => 'BE',
            'nameFr' => 'Belgique',
            'nameEn' => 'Belgium',
        ],
        [
            'code' => 'FR',
            'nameFr' => 'France',
            'nameEn' => 'France',
        ],
        [
            'code' => 'ES',
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
        ],
        [
            'code' => 'IT',
            'nameFr' => 'Italie',
            'nameEn' => 'Italy',
        ],
        [
            'code' => 'CH',
            'nameFr' => 'Suisse',
            'nameEn' => 'Switzerland',
        ],
        [
            'code' => 'TH',
            'nameFr' => 'Thailande',
            'nameEn' => 'Thailand',
        ],
        [
            'code' => 'MY',
            'nameFr' => 'Malaisie',
            'nameEn' => 'Malaysia',
        ],
        [
            'code' => 'ID',
            'nameFr' => 'Indonésie',
            'nameEn' => 'Indonesia',
        ],
        [
            'code' => 'VN',
            'nameFr' => 'Vietnam',
            'nameEn' => 'Vietnam',
        ],
        [
            'code' => 'LA',
            'nameFr' => 'Laos',
            'nameEn' => 'Laos',
        ],
        [
            'code' => 'CN',
            'nameFr' => 'Chine',
            'nameEn' => 'China',
        ],
        [
            'code' => 'PH',
            'nameFr' => 'Philippines',
            'nameEn' => 'Philippines',
        ],
        [
            'code' => 'TW',
            'nameFr' => 'Taiwan',
            'nameEn' => 'Taiwan',
        ],
        [
            'code' => 'JP',
            'nameFr' => 'Japon',
            'nameEn' => 'Japan',
        ],
        [
            'code' => 'KR',
            'nameFr' => 'Corée',
            'nameEn' => 'Korea',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::COUNTRIES as $item) {
            
            $country = new Country();

            $country->setCode($item['code']);
            $country->setNameFr($item['nameFr']);
            $country->setNameEn($item['nameEn']);

            $this->addReference('country-' . $item['nameEn'], $country);
        
            $manager->persist($country);
        }

        $manager->flush();
    }
}
