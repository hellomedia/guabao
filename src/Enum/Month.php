<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Month: int implements TranslatableInterface
{
    use EnumUtilsTrait;

    // Ordering
    // OfferType::cases() returns an array of cases, in order of declaration.

    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::JANUARY  => $translator->trans('month.january', domain: 'enum', locale: $locale),
            self::FEBRUARY  => $translator->trans('month.february', domain: 'enum', locale: $locale),
            self::MARCH  => $translator->trans('month.march', domain: 'enum', locale: $locale),
            self::APRIL  => $translator->trans('month.april', domain: 'enum', locale: $locale),
            self::MAY  => $translator->trans('month.may', domain: 'enum', locale: $locale),
            self::JUNE  => $translator->trans('month.june', domain: 'enum', locale: $locale),
            self::JULY  => $translator->trans('month.july', domain: 'enum', locale: $locale),
            self::AUGUST  => $translator->trans('month.august', domain: 'enum', locale: $locale),
            self::SEPTEMBER  => $translator->trans('month.september', domain: 'enum', locale: $locale),
            self::OCTOBER  => $translator->trans('month.october', domain: 'enum', locale: $locale),
            self::NOVEMBER  => $translator->trans('month.november', domain: 'enum', locale: $locale),
            self::DECEMBER  => $translator->trans('month.december', domain: 'enum', locale: $locale),
        };
    }
}