<?php

namespace App\DataFixtures;

use App\DataFixtures\Tag\FoodTagFixtures;
use App\Entity\Cuisine;
use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Tag\FoodTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FoodFixtures extends Fixture implements DependentFixtureInterface
{
    const TOMATO = 'tomato';
    const WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK = 'wontonducksoup';

    private const FOOD = [
        [
            'key' => self::TOMATO,
            'nameFr' => 'Tomate belge',
            'nameEn' => 'Belgian Tomato',
            'tags' => [FoodTagFixtures::HOME_COOKING],
            'cuisine' => CuisineFixtures::BELGIAN,
            'ingredients' => [IngredientFixtures::TOMATO]
        ],
        [
            'key' => self::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'nameFr' => 'Soupe aux wonton et canard',
            'nameEn' => 'Wonton noodle soup with roasted duck',
            'tags' => [FoodTagFixtures::SOUP],
            'cuisine' => CuisineFixtures::THAI,
            'ingredients' => [IngredientFixtures::DUCK, IngredientFixtures::NOODLES]
        ],
    ];

    public function getDependencies(): array
    {
        return [
            IngredientFixtures::class,
            CuisineFixtures::class,
            FoodTagFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::FOOD as $item) {

            $food = new Food;

            $food->setNameFr($item['nameFr']);
            $food->setNameEn($item['nameEn']);

            foreach ($item['tags'] as $foodTag) {
                $food->addTag($this->getReference('foodTag-' . $foodTag, FoodTag::class));
            }

            $food->setCuisine($this->getReference('cuisine-' . $item['cuisine'], Cuisine::class));

            foreach ($item['ingredients'] as $ingredient) {
                $food->addIngredient($this->getReference('ingredient-' . $ingredient, Ingredient::class));
            }

            $this->setReference('food-' . $item['key'], $food);
            
            $manager->persist($food);
        }

        $manager->flush();
    }
}
