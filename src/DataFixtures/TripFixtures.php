<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Tag\TripTag;
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
            TripTagFixtures::class,
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
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_THAILAND_22,
            'nameFr' => 'Thailande / Vietnam / Indonésie',
            'nameEn' => 'Thailand / Vietnam / Indonesia',
            'startedAt' => '06/05/2022',
            'endedAt' => '19/11/2022',
            'countries' => ['Thailand', 'Vietnam', 'Malaysia', 'Indonesia'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_SPAIN_22,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '12/02/2022',
            'endedAt' => '10/03/2022',
            'countries' => ['Spain'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::HIKING, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_VERDON_21,
            'nameFr' => 'Verdon (France)',
            'nameEn' => 'Verdon (France)',
            'startedAt' => '16/09/2021',
            'endedAt' => '25/09/2021',
            'countries' => ['France'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_MORGON_21,
            'nameFr' => 'Morgon (France)',
            'nameEn' => 'Morgon (France)',
            'startedAt' => '20/06/2021',
            'endedAt' => '26/06/2021',
            'countries' => ['France'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
        ],
        [
            'key' => self::TRIP_MARTELANGE_21,
            'nameFr' => 'Martelange',
            'nameEn' => 'Martelange (Belgium)',
            'startedAt' => '29/05/2021',
            'endedAt' => '30/05/2021',
            'countries' => ['Belgium'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
        ],
        [
            'key' => self::TRIP_SART_TILMAN_21,
            'nameFr' => 'Sart-Tilman',
            'nameEn' => 'Sart-Tilman (Belgium)',
            'startedAt' => '15/09/2020',
            'endedAt' => '16/09/2020',
            'countries' => ['Belgium'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_SPAIN_20,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '1/03/2020',
            'endedAt' => '16/03/2020',
            'countries' => ['Spain'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::HIKING, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_SARDAIGNE_19,
            'nameFr' => 'Sardaigne',
            'nameEn' => 'Sardaigne',
            'startedAt' => '11/10/2019',
            'endedAt' => '19/10/2019',
            'countries' => ['Italy'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
        ],
        [
            'key' => self::TRIP_CHINA_18,
            'nameFr' => 'Chine / Taiwan / Vietnam',
            'nameEn' => 'China / Taiwan / Vietnam',
            'startedAt' => '15/10/2018',
            'endedAt' => '15/01/2019',
            'countries' => ['China', 'Taiwan', 'Vietnam'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_SWITZERLAND_18,
            'nameFr' => 'Gruyères',
            'nameEn' => 'Switzerland',
            'startedAt' => '22/08/2018',
            'endedAt' => '29/08/2018',
            'countries' => ['Switzerland'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
        ],
        [
            'key' => self::TRIP_VERCORS_17,
            'nameFr' => 'Vercors',
            'nameEn' => 'Vercors (France)',
            'startedAt' => '12/08/2017',
            'endedAt' => '19/08/2017',
            'countries' => ['France'],
            'tripTags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => 'Une semaine de rando en autonomie dans la plus grande réserve naturelle de France',
            'headlineEn' => '5 day self-supported hike in the largest natural reserve in France',
            'descriptionFr' => "
            Première expérience de rando de 5 jours en autonmie en bivouac !

La réserve naturelle des Hauts Plateaux du Vercors est une endroit unique. Au fil des jours, mon pote David et moi avons perdu le contact avec la civilisation.

La première nuit fut froide et humide. Nous n'avions pas d 'expérience du bivouac en hamac et n'étions pas équippé de protection pour le bas du hamac .

                Un autre soir,
            notre campement a été encerlé par un troupeau de moutons et reçu la visite d'un grand animal (sûrement un cerf) au milieu de la nuit.

A ce jour, ma rando la plus immersive et sans doute la plus belle!",
            'descriptionEn' => "First experience of self-supported hike!

In the largest natural reserve in France, my friend David and I lost contact with civilization for 5 days.

Our first night was cold and humid, as we had no experience with hamock camping and didn't know about underquilt.

The following nights, we used our survival kit to keep us warm.

Managing water was a challenge, as the water points were rare in the reserve.

One evening, our camp was encircled by a herd of sheep and visited by a large animal (probably a deer) in the middle of the night.

We finished the trek without a way to cook our food.

I felt connected to nature like never before.

To this day, my most stunning and immersive hiking experience!",
        ],
        [
            'key' => self::TRIP_PHILIPPINES_15,
            'nameFr' => 'Philippines',
            'nameEn' => 'Philippines',
            'startedAt' => '07/03/2015',
            'endedAt' => '30/03/2015',
            'countries' => ['China', 'Philippines'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
        ],
        [
            'key' => self::TRIP_PHILIPPINES_12,
            'nameFr' => 'Corée / Philippines',
            'nameEn' => 'Korea / Philippines',
            'startedAt' => '04/12/2012',
            'endedAt' => '11/01/2013',
            'countries' => ['Korea', 'Philippines'],
            'tripTags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
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

            $trip->setHeadlineFr($item['headlineFr'] ?? '');
            $trip->setHeadlineEn($item['headlineEn'] ?? '');
            
            $trip->setDescriptionFr($item['descriptionFr'] ?? '');
            $trip->setDescriptionEn($item['descriptionEn'] ?? '');

            $this->setReference($item['key'], $trip);
            
            foreach ($item['countries'] as $country) {
                $trip->addCountry($this->getReference('country-' . $country, Country::class));
            }

            foreach ($item['tripTags'] as $tag) {
                $trip->addTripTag($this->getReference('triptag-' . $tag, TripTag::class));
            }

            $manager->persist($trip);
        }

        $manager->flush();
    }
}
