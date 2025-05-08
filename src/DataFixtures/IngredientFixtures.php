<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    const TOMATO = 'tomato';
    const APPLE = 'apple';
    const MANGO = 'mango';
    const PEAR = 'pear';
    const STRAWBERRY = 'strawberry';
    const SWEET_POTATO = 'sweetpotato';
    const LENTILS = 'lentils';
    const FETA = 'feta';
    const TARO = 'taro';
    const CINAMON = 'cinamon';
    const EGGS = 'eggs';
    const CHOCOLATE = 'chocolate';

    private const array INGREDIENTS = [
        [
            'key' => self::TOMATO,
            'nameFr' => 'Tomate',
            'nameEn' => 'Tomato',
        ],
        [
            'key' => self::APPLE,
            'nameFr' => 'Pomme',
            'nameEn' => 'Apple',
        ],
        [
            'key' => self::PEAR,
            'nameFr' => 'Poire',
            'nameEn' => 'Pear',
        ],
        [
            'key' => self::STRAWBERRY,
            'nameFr' => 'Fraise',
            'nameEn' => 'Strawberry',
        ],
        [
            'key' => self::MANGO,
            'nameFr' => 'Mangue',
            'nameEn' => 'Mango',
        ],
        [
            'key' => self::SWEET_POTATO,
            'nameFr' => 'Patate douce',
            'nameEn' => 'Sweet Potato',
        ],
        [
            'key' => self::LENTILS,
            'nameFr' => 'Lentilles',
            'nameEn' => 'Lentils',
        ],
        [
            'key' => self::TARO,
            'nameFr' => 'Taro',
            'nameEn' => 'Taro',
        ],
        [
            'key' => self::CINAMON,
            'nameFr' => 'Canelle',
            'nameEn' => 'Cinamon',
        ],
        [
            'key' => self::EGGS,
            'nameFr' => 'Oeufs',
            'nameEn' => 'Eggs',
        ],
        [
            'key' => self::FETA,
            'nameFr' => 'Feta',
            'nameEn' => 'Feta',
        ],
        [
            'key' => self::CHOCOLATE,
            'nameFr' => 'Chocolat',
            'nameEn' => 'Chocolate',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::INGREDIENTS as $item) {
            
            $ingredient = new Ingredient;

            $ingredient->setNameFr($item['nameFr']);
            $ingredient->setNameEn($item['nameEn']);

            $this->setReference('ingredient-' . $item['key'], $ingredient);
        
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
