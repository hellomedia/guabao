<?php

namespace App\Twig\Extension;

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('date', [$this, 'dateFilter'], ['needs_environment' => true]),
            new TwigFilter('chat_date', [$this, 'chatDate']),
            new TwigFilter('short_date', [$this, 'shortDate']),
            new TwigFilter('day_and_month', [$this, 'dayAndMonth']),
            new TwigFilter('day_and_month_with_day_of_week', [$this, 'dayAndMonthWithDayOfWeek']),
            new TwigFilter('month_and_year', [$this, 'monthAndYear']),
            new TwigFilter('time', [$this, 'time']),
        ];
    }

    /**
     * Override twig date filter
     * to make it return an empty string if $timestamp is null
     */
    public function dateFilter(TwigEnvironment $twig, $timestamp, $format = 'd/m/Y H:i')
    {
        $result = '';

        if ($timestamp !== null) {
            $result = $twig->getExtension(CoreExtension::class)->formatDate($timestamp, $format);
        }

        return $result;
    }

    /**
     * Format date for calendar, with i18n.
     */
    public function agendaDate(\DateTimeImmutable $datetime, string $variant, string $lang = null, bool $withMinutes = true): ?string
    {
        $formatter = null;
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $timestamp = $datetime->getTimeStamp();

        // DAY
        if ($variant == 'short') {
            $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'ccc');
        } elseif ($variant == 'long') {
            $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'cccc');
        }
        $day = $formatter->format($datetime);

        // DATE
        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
        $date = $formatter->format($datetime);

        if (date('Y', $timestamp) == date('Y')) {
            // THIS YEAR
            $date = $this->_removeYear($date, $lang);
        } else {
            // PREVIOUS YEAR
            $date = $this->_shortenYear($date, $lang);
        }

        // TIME
        if ($withMinutes) {
            $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
        } else {
            $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'HH');
        }
        $time = $formatter->format($datetime);

        // RESULT
        $result = $day . ' ' . $date . ' ' . $time;

        return $result;
    }

    /**
     * Custom date formating with i18n.
     *
     * TODAY         ==> time only      : 14:23 (fr) or 2:23 PM (en)
     * THIS YEAR     ==> day/month only : 28/05 (fr) or 5/28 (en) or 28-05 (nl)
     * PREVIOUS YEAR ==> day/month/year : 28/05/19 (fr)  or 5/28/19 (fr) or 28-05-19 (nl)
     *
     * ** i18n FORMATS **
     * FR: "28/05/2019"
     * EN: "5/28/19"
     * NL: "28-05-19"
     * ZH: "19/5/28" (!!)
     */
    public function chatDate(\DateTimeImmutable $datetime, string $lang = null, array $options = []): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $timestamp = $datetime->getTimeStamp();

        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
        $time = $formatter->format($datetime);

        // TODAY : time only
        if (date('d/m/y', $timestamp) == date('d/m/y')) {
            return $time;
        }

        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);

        $date = $formatter->format($datetime);

        if (date('Y', $timestamp) == date('Y')) {
            // THIS YEAR
            $date = $this->_removeYear($date, $lang);
        } else {
            // PREVIOUS YEAR
            $date = $this->_shortenYear($date, $lang);
        }

        // WITHIN 2 DAYS : keep minutes
        // if ($timestamp > strtotime('- 2 days')) {
        //     return $date . ' ' . $time;
        // }

        // WITHIN 7 DAYS : keep minutes if requested
        if (($options['keep_minutes_longer'] ?? false) && $timestamp > strtotime('- 7 days')) {
            return $date . ' ' . $time;
        }

        return $date;
    }

    public function shortDate(?\DateTimeImmutable $datetime, string $lang = null): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $timestamp = $datetime->getTimeStamp();

        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);

        $date = $formatter->format($datetime);

        if (date('Y', $timestamp) == date('Y')) {
            // THIS YEAR
            $date = $this->_removeYear($date, $lang);
        } else {
            // PREVIOUS YEAR
            $date = $this->_shortenYear($date, $lang);
        }

        return $date;
    }

    /**
     * ex: 15 janvier / January 15
     * 
     * \IntlDateFormatter::LONG gives the year and there is apparently no good way to remove it
     * https://stackoverflow.com/questions/35129924/how-to-format-date-with-a-intldateformatter-using-medium-or-full-datetype-but-w
     * https://stackoverflow.com/questions/69228978/how-do-i-force-phps-intldateformatter-to-ignore-the-year-only-from-the-output/69229859
     * 
     * So we define the pattern for our 3 backend languages and add a sensible default
     */
    public function dayAndMonth(?\DateTimeImmutable $datetime, string $lang = null): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $formatter = match ($lang) {
            'fr' => new \IntlDateFormatter($lang, pattern: 'd MMMM'),
            'en' => new \IntlDateFormatter($lang, pattern: 'MMMM d'),
            'nl' => new \IntlDateFormatter($lang, pattern: 'MMMM d'),
            default => new \IntlDateFormatter($lang, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE),
        };

        $date = $formatter->format($datetime);

        return $date;
    }

    /**
     * ex: Mercredi 15 janvier / Wednesday January 15
     */
    public function dayAndMonthWithDayOfWeek(?\DateTimeImmutable $datetime, string $lang = null): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $formatter = new \IntlDateFormatter($lang, pattern: 'EEEE');
        $dayOfWeek = $formatter->format($datetime);

        $dayAndMonth = $this->dayAndMonth($datetime, $lang);

        return $dayOfWeek . ' ' . $dayAndMonth;
    }

    /**
     * ex: Janvier 2024 / January 2024
     */
    public function monthAndYear(?\DateTimeImmutable $datetime, string $lang = null): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $formatter = new \IntlDateFormatter($lang, pattern: 'MMMM y');

        return $formatter->format($datetime);
    }

    /**
     * ex: 9h00 / 9:00 am 
     */
    public function time(?\DateTimeImmutable $datetime, string $lang = null): ?string
    {
        if ($datetime == null) {
            return null;
        }

        if ($lang == null) {
            $lang = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $formatter = new \IntlDateFormatter($lang, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);

        return $formatter->format($datetime);
    }

    private function _removeYear(string $date, string $lang): string
    {
        $temp = preg_split("/\/|-/", $date);

        switch ($lang) {
            case 'fr': // "28/05/2019" ==> "28/05"
                $date = $temp[0] . '/' . $temp[1];
                break;
            case 'en': // "5/28/19" ==> "5/28"
                $date = $temp[0] . '/' . $temp[1];
                break;
            case 'nl': // "28-05-19" ==> "28-05"
                $date = $temp[0] . '-' . $temp[1];
                break;
            case 'zh': // year first! "19/5/28" ==> "5/28"
                $date = $temp[1] . '/' . $temp[2];
                break;
            default: // other cases : keep untouched
                break;
        }

        return $date;
    }

    private function _shortenYear(string $date, string $lang): string
    {
        switch ($lang) {
            case 'fr': // "28/05/2019" ==> "28/05/19"
                $temp = preg_split("/\/|-/", $date);
                $date = $temp[0] . '/' . $temp[1] . '/' . substr($temp[2], -2);
                break;
        }

        return $date;
    }

    public function getName(): string
    {
        return 'date';
    }
}
