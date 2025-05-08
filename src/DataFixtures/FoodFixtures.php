<?php

namespace App\DataFixtures;

use App\DataFixtures\Tag\FoodTagFixtures;
use App\Entity\Food;
use App\Entity\FoodType;
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
            'type' => FoodTypeFixtures::BELGIAN,
        ],
        [
            'key' => self::WONTON_NOODLE_SOUP_WITH_ROASTED_DUCK,
            'nameFr' => 'Soupe aux wonton et canard',
            'nameEn' => 'Wonton noodle soup with roasted duck',
            'tags' => [FoodTagFixtures::SOUP],
            'type' => FoodTypeFixtures::THAI,
        ],
    ];

    public function getDependencies(): array
    {
        return [
            FoodTypeFixtures::class,
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

            $food->setType($this->getReference('foodType-' . $item['type'], FoodType::class));

            $this->setReference('food-' . $item['key'], $food);
            
            $manager->persist($food);
        }

        $manager->flush();
    }
}
