<?php

namespace App\DataFixtures;

use App\DataFixtures\Tag\TripTagFixtures;
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
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
            // 'headlineFr' => '12 mois de slow travel dans 4 pays',
            // 'headlineEn' => '12 month slow travel in 4 countries',
        ],
        [
            'key' => self::TRIP_THAILAND_22,
            'nameFr' => 'Thailande / Vietnam / Indonésie',
            'nameEn' => 'Thailand / Vietnam / Indonesia',
            'startedAt' => '06/05/2022',
            'endedAt' => '19/11/2022',
            'countries' => ['Thailand', 'Vietnam', 'Malaysia', 'Indonesia'],
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
            // 'headlineFr' => 'Long séjours à Chiang Mai, Da Nang et Bandung',
            // 'headlineEn' => 'Long stays in Chiang Mai, Da Nang and Bandung',
        ],
        [
            'key' => self::TRIP_SPAIN_22,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '12/02/2022',
            'endedAt' => '10/03/2022',
            'countries' => ['Spain'],
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::HIKING, TripTagFixtures::SOLO],
            'headlineFr' => '1 mois dans un village de l\'arrière pays et 1 rando de 3 jours',
            'headlineEn' => '1 month in a secluded village and a 3-day hiking trip',
        ],
        [
            'key' => self::TRIP_VERDON_21,
            'nameFr' => 'Verdon (France)',
            'nameEn' => 'Verdon (France)',
            'startedAt' => '16/09/2021',
            'endedAt' => '25/09/2021',
            'countries' => ['France'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::SOLO],
            'headlineFr' => '1 nuit en rando solo',
            'headlineEn' => '1 solo hiking night in the hills',
        ],
        [
            'key' => self::TRIP_MORGON_21,
            'nameFr' => 'Morgon (France)',
            'nameEn' => 'Morgon (France)',
            'startedAt' => '20/06/2021',
            'endedAt' => '26/06/2021',
            'countries' => ['France'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => '5 jours de rando en autonomie',
            'headlineEn' => '5 day self-supported hike',
        ],
        [
            'key' => self::TRIP_MARTELANGE_21,
            'nameFr' => 'Martelange',
            'nameEn' => 'Martelange (Belgium)',
            'startedAt' => '29/05/2021',
            'endedAt' => '30/05/2021',
            'countries' => ['Belgium'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => 'Nuit en hamac dans les bois',
            'headlineEn' => 'One night of camping in the woods',
        ],
        [
            'key' => self::TRIP_SART_TILMAN_21,
            'nameFr' => 'Sart-Tilman',
            'nameEn' => 'Sart-Tilman (Belgium)',
            'startedAt' => '15/09/2020',
            'endedAt' => '16/09/2020',
            'countries' => ['Belgium'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::SOLO],
            'headlineFr' => 'Nuit en hamac dans les bois du Sart-Tilman',
            'headlineEn' => 'Solo night in the local woods',
        ],
        [
            'key' => self::TRIP_SPAIN_20,
            'nameFr' => 'Espagne',
            'nameEn' => 'Spain',
            'startedAt' => '1/03/2020',
            'endedAt' => '16/03/2020',
            'countries' => ['Spain'],
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::HIKING, TripTagFixtures::SOLO],
            'headlineFr' => '2 semaines dans un village et rando solo de 2 jours',
            'headlineEn' => '2 weeks in a secluded village and 2-day solo hike',
        ],
        [
            'key' => self::TRIP_SARDAIGNE_19,
            'nameFr' => 'Sardaigne',
            'nameEn' => 'Sardaigne',
            'startedAt' => '11/10/2019',
            'endedAt' => '19/10/2019',
            'countries' => ['Italy'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => '5 jours de rando en autonomie',
            'headlineEn' => '5 day self-supported hike',
        ],
        [
            'key' => self::TRIP_CHINA_18,
            'nameFr' => 'Chine / Taiwan / Vietnam',
            'nameEn' => 'China / Taiwan / Vietnam',
            'startedAt' => '15/10/2018',
            'endedAt' => '15/01/2019',
            'countries' => ['China', 'Taiwan', 'Vietnam'],
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
            'headlineFr' => 'Premier long trip de plusieurs mois',
            'headlineEn' => 'First multiple-month trip',
        ],
        [
            'key' => self::TRIP_SWITZERLAND_18,
            'nameFr' => 'Gruyères',
            'nameEn' => 'Switzerland',
            'startedAt' => '22/08/2018',
            'endedAt' => '29/08/2018',
            'countries' => ['Switzerland'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => '5 jours de rando en autonomie',
            'headlineEn' => '5 day self-supported hike',
        ],
        [
            'key' => self::TRIP_VERCORS_17,
            'nameFr' => 'Vercors',
            'nameEn' => 'Vercors (France)',
            'startedAt' => '12/08/2017',
            'endedAt' => '19/08/2017',
            'countries' => ['France'],
            'tags' => [TripTagFixtures::HIKING, TripTagFixtures::WITH_FRIENDS],
            'headlineFr' => '5 jours de rando en autonomie',
            'headlineEn' => '5 day self-supported hike',
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
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
            'headlineFr' => '3 semaines dans un petit resort isolé',
            'headlineEn' => '3 weeks in a small secluded resort',
        ],
        [
            'key' => self::TRIP_PHILIPPINES_12,
            'nameFr' => 'Corée / Philippines',
            'nameEn' => 'Korea / Philippines',
            'startedAt' => '04/12/2012',
            'endedAt' => '11/01/2013',
            'countries' => ['Korea', 'Philippines'],
            'tags' => [TripTagFixtures::SLOW_TRAVEL, TripTagFixtures::SOLO],
            'headlineFr' => '1 mois à la découverte de Séoul et de Bacolod',
            'headlineEn' => '1 month between Seoul and Bacolod',
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

            foreach ($item['tags'] as $tag) {
                $trip->addTag($this->getReference('triptag-' . $tag, TripTag::class));
            }

            $manager->persist($trip);
        }

        $manager->flush();
    }
}
