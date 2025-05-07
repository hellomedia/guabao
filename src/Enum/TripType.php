<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum TripType: string implements TranslatableInterface
{
    use EnumUtilsTrait;

    // Ordering
    // Condition::cases() returns an array of cases, in order of declaration.
    case HIKING_TRIP = 'S';
    case SLOW_TRAVEL = 'H';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::HIKING_TRIP  => $translator->trans('trip.hiking_trip', domain: 'enum', locale: $locale),
            self::SLOW_TRAVEL => $translator->trans('trip.slow_travel', domain: 'enum', locale: $locale),
        };
    }
}