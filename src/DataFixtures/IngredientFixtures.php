<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    // favourites
    const ZUCHINI = 'zuchini';
    const ADVOCADO = 'advocado';
    const APPLE = 'apple';
    const MANGO = 'mango';
    const PEAR = 'pear';
    const STRAWBERRY = 'strawberry';
    const SWEET_POTATO = 'sweetpotato';
    const LENTILS = 'lentils';
    const TARO = 'taro';
    const CINAMON = 'cinamon';
    const EGGS = 'eggs';
    const CHOCOLATE = 'chocolate';
    const MUNGBEAN = 'mungbean';
    
    // other
    const NOODLES = 'noodles';
    const SPAGHETTI = 'spaghetti';
    const TOMATO = 'tomato';
    const FETA = 'feta';
    const DUCK = 'duck';

    private const array INGREDIENTS = [
        [
            'key' => self::ZUCHINI,
            'nameFr' => 'Courgette',
            'nameEn' => 'Zuchini',
            'favourite' => true,
        ],
        [
            'key' => self::ADVOCADO,
            'nameFr' => 'Avocat',
            'nameEn' => 'Advocado',
            'favourite' => true,
        ],
        [
            'key' => self::APPLE,
            'nameFr' => 'Pomme',
            'nameEn' => 'Apple',
            'favourite' => true,
        ],
        [
            'key' => self::PEAR,
            'nameFr' => 'Poire',
            'nameEn' => 'Pear',
            'favourite' => true,
        ],
        [
            'key' => self::STRAWBERRY,
            'nameFr' => 'Fraise',
            'nameEn' => 'Strawberry',
            'favourite' => true,
        ],
        [
            'key' => self::MANGO,
            'nameFr' => 'Mangue',
            'nameEn' => 'Mango',
            'favourite' => true,
        ],
        [
            'key' => self::SWEET_POTATO,
            'nameFr' => 'Patate douce',
            'nameEn' => 'Sweet Potato',
            'favourite' => true,
        ],
        [
            'key' => self::LENTILS,
            'nameFr' => 'Lentilles',
            'nameEn' => 'Lentils',
            'favourite' => true,
        ],
        [
            'key' => self::TARO,
            'nameFr' => 'Taro',
            'nameEn' => 'Taro',
            'favourite' => true,
        ],
        [
            'key' => self::CINAMON,
            'nameFr' => 'Canelle',
            'nameEn' => 'Cinamon',
            'favourite' => true,
        ],
        [
            'key' => self::EGGS,
            'nameFr' => 'Oeufs',
            'nameEn' => 'Eggs',
            'favourite' => true,
        ],
        [
            'key' => self::CHOCOLATE,
            'nameFr' => 'Chocolat',
            'nameEn' => 'Chocolate',
            'favourite' => true,
        ],
        [
            'key' => self::MUNGBEAN,
            'nameFr' => 'Mungbean',
            'nameEn' => 'Mungbean',
            'favourite' => true,
        ],
        [
            'key' => self::FETA,
            'nameFr' => 'Feta',
            'nameEn' => 'Feta',
            'favourite' => false,
        ],
        [
            'key' => self::TOMATO,
            'nameFr' => 'Tomate',
            'nameEn' => 'Tomato',
            'favourite' => false,
        ],
        [
            'key' => self::DUCK,
            'nameFr' => 'Canard',
            'nameEn' => 'Duck',
            'favourite' => false,
        ],
        [
            'key' => self::NOODLES,
            'nameFr' => 'Nouilles',
            'nameEn' => 'Noodles',
            'favourite' => false,
        ],
        [
            'key' => self::SPAGHETTI,
            'nameFr' => 'Spaghetti',
            'nameEn' => 'Spaghetti',
            'favourite' => false,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::INGREDIENTS as $item) {
            
            $ingredient = new Ingredient;

            $ingredient->setNameFr($item['nameFr']);
            $ingredient->setNameEn($item['nameEn']);

            $ingredient->setFavourite($item['favourite']);

            $this->setReference('ingredient-' . $item['key'], $ingredient);
        
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
