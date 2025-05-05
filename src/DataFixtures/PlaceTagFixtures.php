<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Tag\PlaceTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceTagFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }

    private const array PLACE_TAGS = [
        'Belgium' => [
            [
                'nameFr' => 'Liège',
                'nameEn' => 'Liege'
            ],
        ],
        'France' => [
            [
                'nameFr' => 'Vercors',
                'nameEn' => 'Vercors'
            ],
            [
                'nameFr' => 'Verdon',
                'nameEn' => 'France'
            ],
        ],
        'Thailand' => [
            [
                'nameFr' => 'Chiang Mai',
                'nameEn' => 'Chiang Mai',
            ],
            [
                'nameFr' => 'Koh Lanta',
                'nameEn' => 'Koh Lanta',
            ],
            [
                'nameFr' => 'Huai Yang',
                'nameEn' => 'Huai Yang',
            ],
            [
                'nameFr' => 'Ban Krut',
                'nameEn' => 'Ban Krut',
            ],
        ],
        'Indonesia' => [
            [
                'nameFr' => 'Bandung',
                'nameEn' => 'Bandung',
            ],
        ],
        'Malaysia' => [
            [
                'nameFr' => 'Penang',
                'nameEn' => 'Penang',
            ],
            [
                'nameFr' => 'George Town',
                'nameEn' => 'George Town',
            ],
            [
                'nameFr' => 'Kunlim',
                'nameEn' => 'Kunlim',
            ],
            [
                'nameFr' => 'Ipoh',
                'nameEn' => 'Ipoh',
            ],
            [
                'nameFr' => 'Kuala Lumpur',
                'nameEn' => 'Kuala Lumpur',
            ],
            [
                'nameFr' => 'Kota Kinabalu',
                'nameEn' => 'Kota Kinabalu',
            ],
            [
                'nameFr' => 'Pituru',
                'nameEn' => 'Pituru',
            ],
            [
                'nameFr' => 'Sabah',
                'nameEn' => 'Sabah',
            ],
        ],
        'Laos' => [
            [
                'nameFr' => 'Vientiane',
                'nameEn' => 'Vientiane',
            ],
        ],
        'Vietnam' => [
            [
                'nameFr' => 'Da Nang',
                'nameEn' => 'Da Nang',
            ],
            [
                'nameFr' => 'Hanoi',
                'nameEn' => 'Hanoi',
            ],
            [
                'nameFr' => 'Ho Chi Minh',
                'nameEn' => 'Ho Chi Minh',
            ],
            [
                'nameFr' => 'Phu Quoc',
                'nameEn' => 'Phu Quoc',
            ],
        ],
        'China' => [
            [
                'nameFr' => 'Hong Kong',
                'nameEn' => 'Hong Kong',
            ],
            [
                'nameFr' => 'Shenzhen',
                'nameEn' => 'Shenzhen',
            ],
            [
                'nameFr' => 'Yangshuo',
                'nameEn' => 'Yangshuo',
            ],
            [
                'nameFr' => 'Shanghai',
                'nameEn' => 'Shanghai',
            ],
            [
                'nameFr' => 'Changsha',
                'nameEn' => 'Changsha',
            ],
            [
                'nameFr' => 'Kunming',
                'nameEn' => 'Kunming',
            ],
            [
                'nameFr' => 'Xi\'An',
                'nameEn' => 'Xi\'An',
            ],
            [
                'nameFr' => 'Zhenzhou',
                'nameEn' => 'Zhenzhou',
            ],
            [
                'nameFr' => 'Pingdingshan',
                'nameEn' => 'Pingdingshan',
            ],
            [
                'nameFr' => 'Henan',
                'nameEn' => 'Henan',
            ],
            [
                'nameFr' => 'Songshan',
                'nameEn' => 'Songshan',
            ],
            [
                'nameFr' => 'Taishan',
                'nameEn' => 'Taishan',
            ],
            [
                'nameFr' => 'Pékin',
                'nameEn' => 'Beijing',
            ],
        ],
        'Philippines' => [
            [
                'nameFr' => 'Bacolod',
                'nameEn' => 'Bacolod',
            ],
            [
                'nameFr' => 'Sipalay',
                'nameEn' => 'Sipalay',
            ],
            [
                'nameFr' => 'Dumaguete',
                'nameEn' => 'Dumaguete',
            ],
            [
                'nameFr' => 'Negros',
                'nameEn' => 'Negros',
            ],
            [
                'nameFr' => 'Cebu',
                'nameEn' => 'Cebu',
            ],
        ],
        'Taiwan' => [
            [
                'nameFr' => 'Taipei',
                'nameEn' => 'Taipei',
            ],
            [
                'nameFr' => 'Taichung',
                'nameEn' => 'Taichung',
            ],
        ],
        'Japan' =>  [
            [
                'nameFr' => 'Kirishima',
                'nameEn' => 'Kirishima',
            ],
            [
                'nameFr' => 'Kobayashi',
                'nameEn' => 'Kobayashi',
            ],
            [
                'nameFr' => 'Miyakonjo',
                'nameEn' => 'Miyakonjo',
            ],
            [
                'nameFr' => 'Miyazaki',
                'nameEn' => 'Miyakaki',
            ],
            [
                'nameFr' => 'Bungo Ono',
                'nameEn' => 'Bungo Ono',
            ],
            [
                'nameFr' => 'Oita',
                'nameEn' => 'Oita',
            ],
            [
                'nameFr' => 'Fukuoka',
                'nameEn' => 'Fukuoka',
            ],
            [
                'nameFr' => 'Kyushu',
                'nameEn' => 'Kyushu',
            ],
            [
                'nameFr' => 'Osaka',
                'nameEn' => 'Osaka',
            ],
        ],
        'Korea' => [
            [
                'nameFr' => 'Séoul',
                'nameEn' => 'Seoul',
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::PLACE_TAGS as $country => $placeTags) {

            foreach($placeTags as $item) {
                $placeTag = new PlaceTag();
    
                $placeTag->setNameFr($item['nameFr']);
                $placeTag->setNameEn($item['nameEn']);
    
                $placeTag->setCountry($this->getReference('country-' . $country, Country::class));
    
                $this->addReference('placeTag-' . $item['nameEn'], $placeTag);
            
                $manager->persist($placeTag);
            }
        }

        $manager->flush();
    }
}
