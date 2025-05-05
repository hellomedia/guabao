<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Trip;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TripFixtures extends Fixture implements DependentFixtureInterface
{
    const TRIP_CHINA_23 = 'china-23';
    const TRIP_THAILAND_22 = 'thailand-22';
    const TRIP_SPAIN_22 = 'spain-22';
    const TRIP_VERDON_21 = 'verdon-21';
    const TRIP_MORGON_21 = 'morgon-21';
    const TRIP_MARTELANGE_21 = 'martelange-21';
    const TRIP_SART_TILMAN_21 = 'sart-21';
    const TRIP_SPAIN_20 = 'spain-20';
    const TRIP_SARDAIGNE_19 = 'sardaigne-19';
    const TRIP_CHINA_18 = 'china-18';
    const TRIP_SWITZERLAND_18 = 'swiss-18';
    const TRIP_VERCORS_17 = 'vercors-17';
    const TRIP_PHILIPPINES_15 = 'phil-15';
    const TRIP_PHILIPPINES_12 = 'phil-12';

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }

    public const array TRIPS = [
        [
            'key' => self::TRIP_CHINA_23,
            'nameFr' => 'Chine / Thailande / Malaisie / Japon',
            'nameEn' => 'China / Thailand / Malaysia / Japan',
            'startedAt' => '14/09/2023',
            'endedAt' => '29/08/2024',
            'countries' => ['China', 'Thailand', 'Malaysia', 'Laos', 'Japan'],
        ],
        [
            'key' => self::TRIP_THAILAND_22,
            'nameFr' => 'Thailande / Vietnam / Indonésie',
            'nameEn' => 'Thailand / Vietnam / Indonesia',
            'startedAt' => '06/05/2022',
            'endedAt' => '19/11/2022',
            'countries' => ['Thailand', 'Vietnam', 'Malaysia', 'Indonesia'],
        ],
        [
            'key' => self::TRIP_SPAIN_22,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '12/02/2022',
            'endedAt' => '10/03/2022',
            'countries' => ['Spain'],
        ],
        [
            'key' => self::TRIP_VERDON_21,
            'nameFr' => 'Verdon (France)',
            'nameEn' => 'Verdon (France)',
            'startedAt' => '16/09/2021',
            'endedAt' => '25/09/2021',
            'countries' => ['France'],
        ],
        [
            'key' => self::TRIP_MORGON_21,
            'nameFr' => 'Morgon (France)',
            'nameEn' => 'Morgon (France)',
            'startedAt' => '20/06/2021',
            'endedAt' => '26/06/2021',
            'countries' => ['France'],
        ],
        [
            'key' => self::TRIP_MARTELANGE_21,
            'nameFr' => 'Martelange',
            'nameEn' => 'Martelange (Belgium)',
            'startedAt' => '29/05/2021',
            'endedAt' => '30/05/2021',
            'countries' => ['Belgium'],
        ],
        [
            'key' => self::TRIP_SART_TILMAN_21,
            'nameFr' => 'Sart-Tilman',
            'nameEn' => 'Sart-Tilman (Belgium)',
            'startedAt' => '15/09/2020',
            'endedAt' => '16/09/2020',
            'countries' => ['Belgium'],
        ],
        [
            'key' => self::TRIP_SPAIN_20,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '1/03/2020',
            'endedAt' => '16/03/2020',
            'countries' => ['Spain'],
        ],
        [
            'key' => self::TRIP_SARDAIGNE_19,
            'nameFr' => 'Sardaigne',
            'nameEn' => 'Sardaigne',
            'startedAt' => '11/10/2019',
            'endedAt' => '19/10/2019',
            'countries' => ['Italy'],
        ],
        [
            'key' => self::TRIP_CHINA_18,
            'nameFr' => 'Chine / Taiwan / Vietnam',
            'nameEn' => 'China / Taiwan / Vietnam',
            'startedAt' => '15/10/2018',
            'endedAt' => '15/01/2019',
            'countries' => ['China', 'Taiwan', 'Vietnam'],
        ],
        [
            'key' => self::TRIP_SWITZERLAND_18,
            'nameFr' => 'Gruyères',
            'nameEn' => 'Switzerland',
            'startedAt' => '22/08/2018',
            'endedAt' => '29/08/2018',
            'countries' => ['Switzerland'],
        ],
        [
            'key' => self::TRIP_VERCORS_17,
            'nameFr' => 'Vercors',
            'nameEn' => 'Vercors (France)',
            'startedAt' => '12/08/2017',
            'endedAt' => '19/08/2017',
            'countries' => ['France'],
        ],
        [
            'key' => self::TRIP_PHILIPPINES_15,
            'nameFr' => 'Philippines',
            'nameEn' => 'Philippines',
            'startedAt' => '07/03/2015',
            'endedAt' => '30/03/2015',
            'countries' => ['China', 'Philippines'],
        ],
        [
            'key' => self::TRIP_PHILIPPINES_12,
            'nameFr' => 'Corée / Philippines',
            'nameEn' => 'Korea / Philippines',
            'startedAt' => '04/12/2012',
            'endedAt' => '11/01/2013',
            'countries' => ['Korea', 'Philippines'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::TRIPS as $item) {
            
            $trip = new Trip();

            $trip->setKey($item['key']);
            $trip->setNameFr($item['nameFr']);
            $trip->setNameEn($item['nameEn']);

            $trip->setStartedAt(DateTimeImmutable::createFromFormat('d/m/Y', $item['startedAt']));
            $trip->setEndedAt(DateTimeImmutable::createFromFormat('d/m/Y', $item['endedAt']));

            $this->setReference($item['key'], $trip);
            
            foreach ($item['countries'] as $country) {
                $trip->addCountry($this->getReference('country-' . $country, Country::class));
            }

            $manager->persist($trip);
        }

        $manager->flush();
    }
}
