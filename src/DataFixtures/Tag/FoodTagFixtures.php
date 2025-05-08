<?php

namespace App\DataFixtures\Tag;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodTagFixtures extends Fixture
{
    const BREAKFAST = 'breakfast';
    const DISH = 'dish';
    const DESSERT = 'dessert';
    const BAKERY = 'bakery';
    const FRUIT = 'fruit';
    const SOUP = 'soup';
    const DRINK = 'drink';
    const STREETFOOD = 'streefood';
    const HEALTHY = 'healthy';
    const HOME_COOKING = 'homecooking';
    const VEGETARIAN = 'vege';
    const GROCERIES = 'groceries';

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
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::FOOD_TAGS as $item) {
            
            $foodTag = new FoodTag();

            $foodTag->setNameFr($item['nameFr']);
            $foodTag->setNameEn($item['nameEn']);

            $this->setReference('foodTag-' . $item['key'], $foodTag);
        
            $manager->persist($foodTag);
        }

        $manager->flush();
    }
}
