<?php

namespace App\Helper;

use App\Enum\DateIntervalType;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateHelper
{
    public function __construct(
        private TranslatorInterface $translator,
    )
    {
        
    }
    public static function calculateExpirationDate(DateIntervalType $intervalType, int $duration, ?DateTimeImmutable $start = new DateTimeImmutable()): DateTimeImmutable
    {
        // https://www.php.net/manual/en/dateinterval.construct.php
        $intervalString = 'P' . $duration . $intervalType->value;

        $interval = new DateInterval($intervalString);
        
        return $start->add($interval);
    }

    /**
     * Returns difference between 2 dates in number of days
     * 
     * If withSign = false, returns an int
     * If withSign = true, returns value as a string prefixed with +/-
     * 
     * NB: origin->diff(target) > 0  if target > origin
     */
    public static function diffDates(DateTimeImmutable $origin, DateTimeImmutable $target, ?bool $withSign = false): int|string
    {
        if ($withSign) {
            return $origin->diff($target)->format('%R%a');
        }

        return (int) $origin->diff($target)->format('%a');
    }

    /**
     * Returns approximate duration in months, weeks, days
     * between start date and end date
     */
    public function getApproximateDuration(DateTimeImmutable $start, DateTimeImmutable $end): string
    {
        $interval = $end->diff($start);

        $translator = $this->translator;
        $days = $interval->days;

        if ($days > 380) {
            return 'more than 1 year';
        }
        if ($days > 350 && $days <= 380) {
            return $translator->trans('interval.months', ['%count%' => 12], domain: 'duration');
        }
        if ($days > 62 && $days <= 350) {
            return $translator->trans('interval.months', ['%count%' => $interval->format('%m')], domain: 'duration');
        }
        if ($days > 50 && $days <= 62) {
            return $translator->trans('interval.months', ['%count%' => 2], domain: 'duration');
        }
        if ($days > 35 && $days <= 50) {
            return $translator->trans('interval.weeks', ['%count%' => intdiv($days, 7)], domain: 'duration');
        }
        if ($days > 25 && $days <= 35) {
            return $translator->trans('interval.months', ['%count%' => 1], domain: 'duration');
        }
        if ($days > 12 && $days <= 25) {
            return $translator->trans('interval.weeks', ['%count%' => intdiv($days, 7)], domain: 'duration');
        }
        if ($days > 8 && $days <= 12) {
            return $translator->trans('interval.days', ['%count%' => 10], domain: 'duration');
        }
        if ($days > 5 && $days <= 8) {
            return $translator->trans('interval.weeks', ['%count%' => 1], domain: 'duration');
        }
        if ($interval->d) {
            return $translator->trans('interval.days', ['%count%' => $interval->d + 1], domain: 'duration');
        }

        return $translator->trans('interval.days', ['%count%' => 1]);
    }

    public static function isPast(DateTimeImmutable $date): bool
    {
        return self::diffDates(new DateTimeImmutable('now'), $date, true) < 0;
    }

    public static function isFuture(DateTimeImmutable $date): bool
    {
        return self::diffDates(new DateTimeImmutable('now'), $date, true) >= 0;
    }

    /**
     * Get days since event
     * 
     * @var $precision string <'date'|'time'>
     *      'time'   => 
     *         - difference between yesterday at 4pm and today ('now') at 3pm is 0.
     *         - difference between yesterday at 4pm and today ('now') at 5pm is 1.
     *      'date' => 
     *         - difference between yesterday at 4pm (or any other time, all forced to 00:00::00)
     *         and today ('today') (which means today 00:00:00) is 1.
     *         Thus the difference between any event yesterday and 'today' is 1, when called at any time.
     */
    public static function getDaysSince(DateTimeImmutable $event, ?string $precision = 'date'): int
    {
        if ($precision == 'time') {
            return (int) $event->diff(new DateTime('now'))->format('%a');
        }

        return (int) $event->setTime(0, 0)->diff(new DateTime('today'))->format('%a');
    }

    public static function min(DateTimeImmutable $dateTime1, DateTimeImmutable $dateTime2): DateTimeImmutable
    {
        return $dateTime1 < $dateTime2 ? $dateTime1 : $dateTime2;
    }
}
