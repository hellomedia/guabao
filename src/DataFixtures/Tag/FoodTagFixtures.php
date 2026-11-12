<?php

namespace App\DataFixtures\Tag;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodTagFixtures extends Fixture
{
    public const BREAKFAST = 'breakfast';
    public const DISH = 'dish';
    public const DESSERT = 'dessert';
    public const BAKERY = 'bakery';
    public const FRUIT = 'fruit';
    public const SOUP = 'soup';
    public const DRINK = 'drink';
    public const STREETFOOD = 'streefood';
    public const HEALTHY = 'healthy';
    public const DUMPLINGS = 'dumplings';
    public const SANDWHICH = 'sandwhich';
    public const BAO = 'bao';
    public const HOME_COOKING = 'homecooking';
    public const VEGETARIAN = 'vege';
    public const GROCERIES = 'groceries';
    public const DISCOVERY = 'discovery';

    private const array FOOD_TAGS = [
        [
            'key' => self::BREAKFAST,
            'nameFr' => 'Déjeuner',
            'nameEn' => 'Breakfast',
        ],
        [
            'key' => self::DISH,
            'nameFr' => 'Repas',
            'nameEn' => 'Dish',
        ],
        [
            'key' => self::DESSERT,
            'nameFr' => 'Dessert',
            'nameEn' => 'Dessert',
        ],
        [
            'key' => self::BAKERY,
            'nameFr' => 'Boulangerie',
            'nameEn' => 'Bakery',
        ],
        [
            'key' => self::FRUIT,
            'nameFr' => 'Fruit',
            'nameEn' => 'Fruit',
        ],
        [
            'key' => self::SOUP,
            'nameFr' => 'Soupe',
            'nameEn' => 'Soup',
        ],
        [
            'key' => self::DRINK,
            'nameFr' => 'Boisson',
            'nameEn' => 'Drink',
        ],
        [
            'key' => self::STREETFOOD,
            'nameFr' => 'Street food',
            'nameEn' => 'Street food',
        ],
        [
            'key' => self::HEALTHY,
            'nameFr' => 'Healthy',
            'nameEn' => 'Healthy',
        ],
        [
            'key' => self::DUMPLINGS,
            'nameFr' => 'Dumplings',
            'nameEn' => 'Dumplings',
        ],
        [
            'key' => self::BAO,
            'nameFr' => 'Bao',
            'nameEn' => 'Bao',
        ],
        [
            'key' => self::SANDWHICH,
            'nameFr' => 'Sandwhich',
            'nameEn' => 'Sandwhich',
        ],
        [
            'key' => self::HOME_COOKING,
            'nameFr' => 'Home cooking',
            'nameEn' => 'Home cooking',
        ],
        [
            'key' => self::VEGETARIAN,
            'nameFr' => 'Végé',
            'nameEn' => 'Vegetarian',
        ],
        [
            'key' => self::GROCERIES,
            'nameFr' => 'Courses',
            'nameEn' => 'Groceries',
        ],
        [
            'key' => self::DISCOVERY,
            'nameFr' => 'Découverte',
            'nameEn' => 'Discovery',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::FOOD_TAGS as $item) {
            $foodTag = new FoodTag();

            $foodTag->setNameFr($item['nameFr']);
            $foodTag->setNameEn($item['nameEn']);

            $this->setReference('foodTag-' . $item['key'], $foodTag);

            $manager->persist($foodTag);
        }

        $manager->flush();
    }
}
