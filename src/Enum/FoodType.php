<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum FoodType: string implements TranslatableInterface
{
    use EnumUtilsTrait;

    // Ordering
    // Condition::cases() returns an array of cases, in order of declaration.
    case VEGETABLE = 'Vegetable';
    case FRUIT = 'Fruit';
    case LEGUMES = 'Legumes';
    case FISH = 'Fish';
    case MEAT = 'Meat';
    case DAIRY = 'Dairy';
    case EGGS = 'Eggs';
    case HERBS_SPICES = 'Herbs and spices';
    case GRAINS_CEREALS = 'Grains and cereals';
    case NUTS_SEEDS = 'Nuts and seeds';
    case PASTA_NOODLES = 'Pasta and noodles';
    case SWEETENERS = 'Sweetners'; // typo in DB left as such
    case CHOCOLATE = 'Chocolate';
    case CONDIMENT_SAUCE = 'Condiment and sauce';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::CHOCOLATE  => $translator->trans('food.chocolate', domain: 'enum', locale: $locale),
            self::DAIRY  => $translator->trans('food.dairy', domain: 'enum', locale: $locale),
            self::EGGS  => $translator->trans('food.eggs', domain: 'enum', locale: $locale),
            self::FISH  => $translator->trans('food.fish', domain: 'enum', locale: $locale),
            self::FRUIT  => $translator->trans('food.fruit', domain: 'enum', locale: $locale),
            self::GRAINS_CEREALS  => $translator->trans('food.grains', domain: 'enum', locale: $locale),
            self::HERBS_SPICES  => $translator->trans('food.herbs', domain: 'enum', locale: $locale),
            self::LEGUMES  => $translator->trans('food.legumes', domain: 'enum', locale: $locale),
            self::MEAT  => $translator->trans('food.meat', domain: 'enum', locale: $locale),
            self::NUTS_SEEDS  => $translator->trans('food.nuts', domain: 'enum', locale: $locale),
            self::PASTA_NOODLES  => $translator->trans('food.pasta', domain: 'enum', locale: $locale),
            self::SWEETENERS  => $translator->trans('food.sweeteners', domain: 'enum', locale: $locale),
            self::VEGETABLE  => $translator->trans('food.vegetable', domain: 'enum', locale: $locale),
            self::CONDIMENT_SAUCE  => $translator->trans('food.condiment_sauce', domain: 'enum', locale: $locale),
        };
    }

    public function getSortOrder(): int
    {
        return match ($this) {
            self::VEGETABLE  => 1,
            self::FRUIT  => 2,
            self::LEGUMES  => 3,
            self::FISH  => 4,
            self::MEAT  => 5,
            self::DAIRY  => 6,
            self::EGGS  => 7,
            self::HERBS_SPICES  => 8,
            self::GRAINS_CEREALS  => 9,
            self::NUTS_SEEDS  => 10,
            self::PASTA_NOODLES  => 11,
            self::SWEETENERS  => 12,
            self::CHOCOLATE  => 13,
            self::CONDIMENT_SAUCE  => 14,
        };
    }
}