<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Level: int implements TranslatableInterface
{
    use EnumUtilsTrait;

    // Ordering
    // OfferType::cases() returns an array of cases, in order of declaration.

    case LEVEL_1 = 1;
    case LEVEL_2 = 2;
    case LEVEL_3 = 3;
    case LEVEL_4 = 4;
    case LEVEL_5 = 5;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::LEVEL_1  => $translator->trans('level.1', domain: 'enum', locale: $locale),
            self::LEVEL_2 => $translator->trans('level.2', domain: 'enum', locale: $locale),
            self::LEVEL_3 => $translator->trans('level.3', domain: 'enum', locale: $locale),
            self::LEVEL_4 => $translator->trans('level.4', domain: 'enum', locale: $locale),
            self::LEVEL_5 => $translator->trans('level.5', domain: 'enum', locale: $locale),
        };
    }
}