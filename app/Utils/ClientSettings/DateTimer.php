<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Exception;
use Illuminate\Support\Str;

class DateTimer
{
    use ClassTrait;

    public const LONG_DATE_FUNCTION = 'longDate';
    public const SHORT_DATE_FUNCTION = 'shortDate';
    public const LONG_TIME_FUNCTION = 'longTime';
    public const SHORT_TIME_FUNCTION = 'shortTime';
    public const DATABASE_FORMAT_DATE = 'Y-m-d';
    public const DATABASE_FORMAT_TIME = 'H:i:s';
    public const DATABASE_FORMAT = DateTimer::DATABASE_FORMAT_DATE . ' ' . DateTimer::DATABASE_FORMAT_TIME;
    public const DAY_TYPE_NONE = 0;
    public const DAY_TYPE_START = -1;
    public const DAY_TYPE_END = 1;
    public const DAY_TYPE_NEXT_START = 2;

    /**
     * @var Carbon
     */
    protected static $now;

    protected $locale;

    protected $locales;

    protected $transLongDate;

    protected $transShortDate;

    protected $transShortMonth;

    protected $transLongTime;

    protected $transShortTime;

    /**
     * Seconds
     *
     * @var float|int
     */
    protected $dateTimeOffset;

    public function __construct(Settings $settings)
    {
        $this->locale = $settings->getLocale();

        $this->transLongDate = 'datetime.formats.long_date_' . $settings->getLongDateFormat();
        $this->transShortDate = 'datetime.formats.short_date_' . $settings->getShortDateFormat();
        $this->transShortMonth = 'datetime.formats.short_month_' . $settings->getShortDateFormat();
        $this->transLongTime = 'datetime.formats.long_time_' . $settings->getLongTimeFormat();
        $this->transShortTime = 'datetime.formats.short_time_' . $settings->getShortTimeFormat();

        $this->dateTimeOffset = $this->parseDateTimeOffsetByTimezone($settings->getTimezone());
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getDateTimeOffset()
    {
        return $this->dateTimeOffset;
    }

    /**
     * @param $time
     * @return Carbon
     * @throws
     */
    protected function toCarbon($time)
    {
        if (is_string($time)) {
            try {
                return (new Carbon($time, new CarbonTimeZone('UTC')))->locale($this->locale);
            }
            catch (Exception $exception) {
                throw AppException::from($exception);
            }
        }

        if ($time instanceof Carbon) {
            return (clone $time)->setTimezone(new CarbonTimeZone('UTC'))->locale($this->locale);
        }

        throw new AppException(self::__transErrorWithModule('time_not_supported'));
    }

    protected function resetLocales($force = false)
    {
        if (empty($this->locales) || $force) {
            $this->locales = [
                'default' => [],
                'trans' => [],
            ];
            $defaultLocales = [];
            for ($i = 1; $i <= 7; ++$i) {
                $defaultLocales[] = trans('datetime.day_' . $i, [], 'en');
                $this->locales['trans'][] = trans('datetime.day_' . $i, [], $this->locale);
            }
            for ($i = 1; $i <= 7; ++$i) {
                $defaultLocales[] = trans('datetime.short_day_' . $i, [], 'en');
                $this->locales['trans'][] = trans('datetime.short_day_' . $i, [], $this->locale);
            }
            for ($i = 1; $i <= 12; ++$i) {
                $defaultLocales[] = trans('datetime.month_' . $i, [], 'en');
                $this->locales['trans'][] = trans('datetime.month_' . $i, [], $this->locale);
            }
            for ($i = 1; $i <= 12; ++$i) {
                $defaultLocales[] = trans('datetime.short_month_' . $i, [], 'en');
                $this->locales['trans'][] = trans('datetime.short_month_' . $i, [], $this->locale);
            }
            foreach (['lm', 'um'] as $c) {
                foreach (['am', 'pm'] as $m) {
                    $defaultLocales[] = trans('datetime.' . $c . '_' . $m, [], 'en');
                    $this->locales['trans'][] = trans('datetime.' . $c . '_' . $m, [], $this->locale);
                }
            }
            uasort($this->locales['trans'], function ($t1, $t2) {
                $t1Length = mb_strlen($t1);
                $t2Length = mb_strlen($t2);
                if ($t1Length != $t2Length) {
                    return $t1Length < $t2Length ? 1 : -1;
                }

                return $t1 == $t2 ? 0 : ($t1 < $t2 ? 1 : -1);
            });
            foreach ($this->locales['trans'] as $key => $text) {
                $this->locales['default'][$key] = $defaultLocales[$key];
            }
        }
    }

    protected function standardizeTime($time)
    {
        $this->resetLocales();
        return str_replace($this->locales['trans'], $this->locales['default'], $time);
    }

    protected function applyStartType(Carbon $time, $start = DateTimer::DAY_TYPE_NONE)
    {
        switch ($start) {
            case static::DAY_TYPE_NEXT_START:
                $time->setTime(0, 0, 0)->addDay();
                break;
            case static::DAY_TYPE_END:
                $time->setTime(23, 59, 59);
                break;
            case static::DAY_TYPE_START:
                $time->setTime(0, 0, 0);
                break;
        }
        return $time;
    }

    #region From Local to UTC Time

    /**
     * @param Carbon|string $time
     * @param int $start
     * @return Carbon
     * @throws
     */
    public function from($time, $start = DateTimer::DAY_TYPE_NONE)
    {
        $time = $this->applyStartType($this->toCarbon($time), $start);
        $time->subSeconds($this->getDateTimeOffset());
        return $time;
    }

    public function fromFormat($format, $time, $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->from(Carbon::createFromFormat($format, $this->standardizeTime($time)), $start);
    }

    public function fromFormatToFormat($format, $time, $toFormat = null, $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->fromFormat($format, $time, $start)->format($toFormat ?: $format);
    }

    public function fromFormatToDatabaseFormat($format, $time, $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->fromFormatToFormat($format, $time, static::DATABASE_FORMAT, $start);
    }

    #endregion

    #region From UTC to Local Time
    /**
     * @param Carbon|string $time
     * @param int $start
     * @return Carbon
     * @throws
     */
    public function getObject($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        $time = $this->toCarbon($time);
        $time->addSeconds($this->getDateTimeOffset());
        return $this->applyStartType($time, $start);
    }

    protected function getBags($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        $time = $this->getObject($time, $start);
        return [
            'd' => $time->format('j'),
            'dd' => $time->format('d'),
            'sd' => trans('datetime.short_day_' . $time->format('N'), [], $this->locale),
            'ld' => trans('datetime.day_' . $time->format('N'), [], $this->locale),
            'm' => $time->format('n'),
            'mm' => $time->format('m'),
            'sm' => trans('datetime.short_month_' . $time->format('n'), [], $this->locale),
            'lm' => trans('datetime.month_' . $time->format('n'), [], $this->locale),
            'yy' => $time->format('y'),
            'yyyy' => $time->format('Y'),
            'h' => $time->format('g'),
            'hh' => $time->format('h'),
            'h2' => $time->format('G'),
            'hh2' => $time->format('H'),
            'ii' => $time->format('i'),
            'ss' => $time->format('s'),
            'ut' => trans('datetime.um_' . $time->format('a'), [], $this->locale),
            'lt' => trans('datetime.lm_' . $time->format('a'), [], $this->locale),
        ];
    }

    protected function getFormatBags()
    {
        return [
            'd' => 'j',
            'dd' => 'd',
            'sd' => 'D',
            'ld' => 'l',
            'm' => 'n',
            'mm' => 'm',
            'sm' => 'M',
            'lm' => 'F',
            'yy' => 'y',
            'yyyy' => 'Y',
            'h' => 'g',
            'hh' => 'h',
            'h2' => 'G',
            'hh2' => 'H',
            'ii' => 'i',
            'ss' => 's',
            'ut' => 'A',
            'lt' => 'a',
        ];
    }

    protected function longDateFromBags(array $bags)
    {
        return trans($this->transLongDate, $bags, $this->locale);
    }

    protected function shortDateFromBags(array $bags)
    {
        return trans($this->transShortDate, $bags, $this->locale);
    }

    protected function shortMonthFromBags(array $bags)
    {
        return trans($this->transShortMonth, $bags, $this->locale);
    }

    protected function longTimeFromBags(array $bags)
    {
        return trans($this->transLongTime, $bags, $this->locale);
    }

    protected function shortTimeFromBags(array $bags)
    {
        return trans($this->transShortTime, $bags, $this->locale);
    }

    public function compound($func1 = DateTimer::SHORT_DATE_FUNCTION, $separation = ' ', $func2 = DateTimer::SHORT_TIME_FUNCTION, $time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        $bags = $this->getBags($time, $start);
        return sprintf('%s%s%s', call_user_func([$this, $func1 . 'FromBags'], $bags), $separation, call_user_func([$this, $func2 . 'FromBags'], $bags));
    }

    public function longDate($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->longDateFromBags($this->getBags($time, $start));
    }

    public function shortDate($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->shortDateFromBags($this->getBags($time, $start));
    }

    public function shortMonth($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->shortMonthFromBags($this->getBags($time, $start));
    }

    public function longTime($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->longTimeFromBags($this->getBags($time, $start));
    }

    public function shortTime($time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->shortTimeFromBags($this->getBags($time, $start));
    }

    public function custom($name, $time = 'now', $start = DateTimer::DAY_TYPE_NONE)
    {
        return trans('datetime.custom_formats.' . $name, $this->getBags($time, $start), $this->locale);
    }

    public function compoundFormat($func1, $separation, $func2)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        return sprintf('%s%s%s', call_user_func([$this, $func1 . 'Format']), $separation, call_user_func([$this, $func2 . 'Format']));
    }

    public function longDateFormat()
    {
        return $this->longDateFromBags($this->getFormatBags());
    }

    public function shortDateFormat()
    {
        return $this->shortDateFromBags($this->getFormatBags());
    }

    public function shortMonthFormat()
    {
        return $this->shortMonthFromBags($this->getFormatBags());
    }

    public function longTimeFormat()
    {
        return $this->longTimeFromBags($this->getFormatBags());
    }

    public function shortTimeFormat()
    {
        return $this->shortTimeFromBags($this->getFormatBags());
    }

    public function customFormat($name)
    {
        return trans('datetime.custom_formats.' . $name, $this->getFormatBags(), $this->locale);
    }

    /**
     * @param string $format
     * @param Carbon|string $time
     * @param int $start
     * @return string
     * @throws
     */
    public function format($format, $time, $start = DateTimer::DAY_TYPE_NONE)
    {
        return $this->getObject($time, $start)->format($format);
    }

    #endregion

    protected function getExampleBags()
    {
        return $this->getBags(static::syncNowObject()->year . '-12-24 08:00:00', true);
    }

    protected function parseDateTimeOffsetByTimezone($timeZone)
    {
        if (empty($timeZone)) {
            return 0;
        }
        if ($timeZone != 'UTC' && Str::startsWith($timeZone, 'UTC')) {
            return floatval(Str::substr($timeZone, 3)) * 3600;
        }
        return (new CarbonTimeZone($timeZone))->getOffset(new Carbon());
    }

    public function getTimezones()
    {
        // UTC
        $timezones = [
            [
                'name' => 'UTC',
                'timezones' => [
                    [
                        'name' => 'UTC',
                        'value' => 'UTC',
                    ],
                ],
            ],
        ];
        // Timezone by UTC offsets
        $utcOffsets = [];
        foreach ($this->getUtcOffsets() as $offset) {
            $offsetValue = 'UTC' . (0 <= $offset ? '+' . $offset : (string)$offset);
            $offsetName = str_replace(['.25', '.5', '.75'], [':15', ':30', ':45'], $offsetValue);
            $utcOffsets[] = [
                'name' => $offsetName,
                'value' => $offsetValue,
            ];
        }
        $timezones[] = [
            'name' => trans('datetime.utc_offsets', [], $this->locale),
            'timezones' => $utcOffsets,
        ];
        // UNIX Timezones
        $unixTimezones = [];
        $currentContinent = null;
        foreach (CarbonTimeZone::listIdentifiers() as $zone) {
            $zonePart = explode('/', $zone);
            $continent = $zonePart[0];

            if ($continent == 'UTC') {
                continue;
            }

            if (!empty($currentContinent) && $continent != $currentContinent) {
                $timezones[] = [
                    'name' => $currentContinent,
                    'timezones' => $unixTimezones,
                ];
                $unixTimezones = [];
            }
            $currentContinent = $continent;
            $city = $zonePart[1] ?? '';
            $subCity = $zonePart[2] ?? '';
            $unixTimezones[] = [
                'name' => str_replace('_', ' ', $city) . (empty($subCity) ? '' : ' - ' . str_replace('_', ' ', $subCity)),
                'value' => $zone,
            ];
        }
        $timezones[] = [
            'name' => $currentContinent,
            'timezones' => $unixTimezones,
        ];
        return $timezones;
    }

    public function getDaysOfWeek()
    {
        $options = [];
        for ($i = 1; $i <= 7; ++$i) {
            $options[] = [
                'value' => $i,
                'name' => trans('datetime.day_' . $i, [], $this->locale),
            ];
        }
        return $options;
    }

    public function getLongDateFormats()
    {
        $options = [];
        for ($i = 0; $i <= 3; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_date_' . $i, $this->getExampleBags(), $this->locale),
            ];
        }
        return $options;
    }

    public function getShortDateFormats()
    {
        $options = [];
        for ($i = 0; $i <= 3; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_date_' . $i, $this->getExampleBags(), $this->locale),
            ];
        }
        return $options;
    }

    public function getLongTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_time_' . $i, $this->getExampleBags(), $this->locale),
            ];
        }
        return $options;
    }

    public function getShortTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_time_' . $i, $this->getExampleBags(), $this->locale),
            ];
        }
        return $options;
    }

    public static function getUtcOffsets()
    {
        return [
            -12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
            0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14,
        ];
    }

    public static function getTimezoneValues()
    {
        // UTC
        $timezones = ['UTC'];
        // Timezone by UTC offsets
        foreach (static::getUtcOffsets() as $offset) {
            $timezones[] = 'UTC' . (0 <= $offset ? '+' . $offset : (string)$offset);
        }
        // UNIX Timezones
        foreach (CarbonTimeZone::listIdentifiers() as $zone) {
            $timezones[] = $zone;
        }
        return $timezones;
    }

    public static function getDaysOfWeekValues()
    {
        return range(1, 7);
    }

    public static function getLongDateFormatValues()
    {
        return range(0, 3);
    }

    public static function getShortDateFormatValues()
    {
        return range(0, 3);
    }

    public static function getLongTimeFormatValues()
    {
        return range(0, 4);
    }

    public static function getShortTimeFormatValues()
    {
        return range(0, 4);
    }

    /**
     * @param bool $reset
     * @return Carbon
     * @throws
     */
    public static function syncNowObject($reset = false)
    {
        if ($reset || empty(static::$now)) {
            static::$now = new Carbon('now', new CarbonTimeZone('UTC'));
        }
        return clone static::$now;
    }

    /**
     * @param string $format
     * @param bool $reset
     * @return string
     * @throws
     */
    public static function syncNowFormat($format, $reset = false)
    {
        return static::syncNowObject($reset)->format($format);
    }

    /**
     * @param bool $reset
     * @return string
     * @throws
     */
    public static function syncNow($reset = false)
    {
        return static::syncNowFormat(static::DATABASE_FORMAT, $reset);
    }
}
