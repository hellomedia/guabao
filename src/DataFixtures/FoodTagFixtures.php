<?php

namespace App\DataFixtures;

use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodTagFixtures extends Fixture
{
    private const array FOOD_TAGS = [
        [
            'nameFr' => 'Déjeuner',
            'nameEn' => 'Breakfast',
        ],
        [
            'nameFr' => 'Repas',
            'nameEn' => 'Dish',
        ],
        [
            'nameFr' => 'Dessert',
            'nameEn' => 'Dessert',
        ],
        [
            'nameFr' => 'Boulangerie',
            'nameEn' => 'Bakery',
        ],
        [
            'nameFr' => 'Fruit',
            'nameEn' => 'Fruit',
        ],
        [
            'nameFr' => 'Soupe',
            'nameEn' => 'Soup',
        ],
        [
            'nameFr' => 'Boisson',
            'nameEn' => 'Drink',
        ],
        [
            'nameFr' => 'Street food',
            'nameEn' => 'Street food',
        ],
        [
            'nameFr' => 'Healthy',
            'nameEn' => 'Healthy',
        ],
        [
            'nameFr' => 'Home cooking',
            'nameEn' => 'Home cooking',
        ],
        [
            'nameFr' => 'Food court',
            'nameEn' => 'Food court',
        ],
        [
            'nameFr' => 'Végé',
            'nameEn' => 'Vegetarian',
        ],
        [
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

            $this->addReference('foodTag-' . $item['nameEn'], $foodTag);
        
            $manager->persist($foodTag);
        }

        $manager->flush();
    }
}
