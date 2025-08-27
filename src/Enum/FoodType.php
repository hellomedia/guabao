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
    case CHOCOLATE = 'Chocolate';
    case DAIRY = 'Dairy';
    case EGGS = 'Eggs';
    case FISH = 'Fish';
    case FRUIT = 'Fruit';
    case GRAINS_CEREALS = 'Grains and cereals';
    case HERBS_SPICES = 'Herbs and spices';
    case LEGUMES = 'Legumes';
    case MEAT = 'Meat';
    case NUTS_SEEDS = 'Nuts and seeds';
    case SWEETNERS = 'Sweetners';
    case VEGETABLE = 'Vegetable';

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
            self::SWEETNERS  => $translator->trans('food.sweetners', domain: 'enum', locale: $locale),
            self::VEGETABLE  => $translator->trans('food.vegetable', domain: 'enum', locale: $locale), 
        };
    }
}