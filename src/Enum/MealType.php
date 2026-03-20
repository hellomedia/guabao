<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum MealType: string implements TranslatableInterface
{
    use EnumUtilsTrait;

    // Ordering
    // Condition::cases() returns an array of cases, in order of declaration.
    case BREAKFAST = 'B';
    case LUNCH = 'L';
    case DINNER = 'D';
    case SNACK = 'S';
    case DRINK = 'K';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::BREAKFAST  => $translator->trans('meal.breakfast', domain: 'enum', locale: $locale),
            self::LUNCH => $translator->trans('meal.lunch', domain: 'enum', locale: $locale),
            self::DINNER => $translator->trans('meal.dinner', domain: 'enum', locale: $locale),
            self::SNACK => $translator->trans('meal.snack', domain: 'enum', locale: $locale),
            self::DRINK => $translator->trans('meal.drink', domain: 'enum', locale: $locale),
        };
    }

    /**
     * Used from getName() in Meal entity
     * where we don't have access to the locale or the translatorInterface
     */
    public function toEnglish(): string
    {
        return match ($this) {
            self::BREAKFAST  => 'Breakfast',
            self::LUNCH => 'Lunch',
            self::DINNER => 'Dinner',
            self::SNACK => 'Snack',
            self::DRINK => 'Drink',
        };
    }
}