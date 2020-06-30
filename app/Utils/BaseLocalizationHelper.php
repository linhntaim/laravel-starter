<?php

namespace App\Utils;

abstract class BaseLocalizationHelper
{
    protected static $instance;

    /**
     * @return BaseLocalizationHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected $fetched;
    protected $fromApp;

    public $_ts;

    public $locale;
    public $country;
    public $timezone;
    public $currency;
    public $numberFormat;
    public $firstDayOfWeek;
    public $longDateFormat;
    public $shortDateFormat;
    public $longTimeFormat;
    public $shortTimeFormat;

    public function __construct()
    {
        $this->fetched = false;
        $this->fromApp = false;

        $this->_ts = 0;
        $this->locale = config('app.locale');
        $this->country = ConfigHelper::get('localization.country');
        $this->timezone = config('app.timezone');
        $this->currency = ConfigHelper::get('localization.currency');
        $this->numberFormat = ConfigHelper::get('localization.number_format');
        $this->firstDayOfWeek = ConfigHelper::get('localization.first_day_of_week');
        $this->longDateFormat = ConfigHelper::get('localization.long_date_format');
        $this->shortDateFormat = ConfigHelper::get('localization.short_date_format');
        $this->longTimeFormat = ConfigHelper::get('localization.long_time_format');
        $this->shortTimeFormat = ConfigHelper::get('localization.short_time_format');
    }

    public function apply()
    {
        ConfigHelper::setCurrentLocale($this->getLocale());
        return $this;
    }

    public function setTimestamp($ts)
    {
        $this->_ts = $ts;
    }

    public function getTimestamp()
    {
        return $this->_ts;
    }

    public function setLocale($locale)
    {
        if (!is_null($locale) && in_array($locale, ConfigHelper::getLocaleCodes())) {
            $this->locale = $locale;
        }
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setCountry($country)
    {
        if (!is_null($country) && in_array($country, ConfigHelper::getCountryCodes())) {
            $this->country = $country;
        }
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setTimezone($timezone)
    {
        if (!is_null($timezone) && in_array($timezone, DateTimeHelper::getTimezoneValues())) {
            $this->timezone = $timezone;
        }
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setCurrency($currency)
    {
        if (!is_null($currency) && in_array($currency, ConfigHelper::getCurrencyCodes())) {
            $this->currency = $currency;
        }
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setNumberFormat($numberFormat)
    {
        if (!is_null($numberFormat) && in_array($numberFormat, ConfigHelper::getNumberFormats())) {
            $this->numberFormat = $numberFormat;
        }
    }

    public function getNumberFormat()
    {
        return $this->numberFormat;
    }

    public function setFirstDayOfWeek($firstDayOfWeek)
    {
        if (!is_null($firstDayOfWeek) && in_array($firstDayOfWeek, DateTimeHelper::getDaysOfWeekValues())) {
            $this->firstDayOfWeek = $firstDayOfWeek;
        }
    }

    public function getFirstDayOfWeek()
    {
        return $this->firstDayOfWeek;
    }

    public function setLongDateFormat($longDateFormat)
    {
        if (!is_null($longDateFormat) && in_array($longDateFormat, DateTimeHelper::getLongDateFormatValues())) {
            $this->longDateFormat = $longDateFormat;
        }
    }

    public function getLongDateFormat()
    {
        return $this->longDateFormat;
    }

    public function setShortDateFormat($shortDateFormat)
    {
        if (!is_null($shortDateFormat) && in_array($shortDateFormat, DateTimeHelper::getShortDateFormatValues())) {
            $this->shortDateFormat = $shortDateFormat;
        }
    }

    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    public function setLongTimeFormat($longTimeFormat)
    {
        if (!is_null($longTimeFormat) && in_array($longTimeFormat, DateTimeHelper::getLongTimeFormatValues())) {
            $this->longTimeFormat = $longTimeFormat;
        }
    }

    public function getLongTimeFormat()
    {
        return $this->longTimeFormat;
    }

    public function setShortTimeFormat($shortTimeFormat)
    {
        if (!is_null($shortTimeFormat) && DateTimeHelper::getShortTimeFormatValues()) {
            $this->shortTimeFormat = $shortTimeFormat;
        }
    }

    public function getShortTimeFormat()
    {
        return $this->shortTimeFormat;
    }

    public function autoFetch()
    {
        return $this->fetchFromRequestHeader()
            ->apply();
    }

    public function fetched()
    {
        return $this->fetched;
    }

    public function fetchFromConfiguration($clientAppId)
    {
        if ($this->fetched) return $this;

        $client = ConfigHelper::getClient($clientAppId);
        $localization = $client['default_localization'];
        if (isset($localization['locale'])) $this->setLocale($localization['locale']);
        if (isset($localization['country'])) $this->setCountry($localization['country']);
        if (isset($localization['timezone'])) $this->setTimezone($localization['timezone']);
        if (isset($localization['currency'])) $this->setCurrency($localization['currency']);
        if (isset($localization['number_format'])) $this->setNumberFormat($localization['number_format']);
        if (isset($localization['first_day_of_week'])) $this->setFirstDayOfWeek($localization['first_day_of_week']);
        if (isset($localization['long_date_format'])) $this->setLongDateFormat($localization['long_date_format']);
        if (isset($localization['short_date_format'])) $this->setShortDateFormat($localization['short_date_format']);
        if (isset($localization['long_time_format'])) $this->setLongTimeFormat($localization['long_time_format']);
        if (isset($localization['short_time_format'])) $this->setShortTimeFormat($localization['short_time_format']);

        $this->fetched = true;

        return $this;
    }

    public function fetchFromRequestHeader()
    {
        if ($this->fetched) return $this;

        $request = request();
        if (!$request->headers->has('Localization')) return $this;

        $localization = json_decode($request->header('Localization'), true);
        if (empty($localization)) return $this;

        if (isset($localization['_from_app'])) $this->fromApp = true;
        if (isset($localization['_ts'])) $this->setTimestamp($localization['_ts']);
        if (isset($localization['locale'])) $this->setLocale($localization['locale']);
        if (isset($localization['country'])) $this->setCountry($localization['country']);
        if (isset($localization['timezone'])) $this->setTimezone($localization['timezone']);
        if (isset($localization['currency'])) $this->setCurrency($localization['currency']);
        if (isset($localization['number_format'])) $this->setNumberFormat($localization['number_format']);
        if (isset($localization['first_day_of_week'])) $this->setFirstDayOfWeek($localization['first_day_of_week']);
        if (isset($localization['long_date_format'])) $this->setLongDateFormat($localization['long_date_format']);
        if (isset($localization['short_date_format'])) $this->setShortDateFormat($localization['short_date_format']);
        if (isset($localization['long_time_format'])) $this->setLongTimeFormat($localization['long_time_format']);
        if (isset($localization['short_time_format'])) $this->setShortTimeFormat($localization['short_time_format']);

        $this->fetched = true;

        return $this;
    }

    public function toArray()
    {
        return [
            'locale' => $this->locale,
            'country' => $this->country,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'number_format' => $this->numberFormat,
            'first_day_of_week' => $this->firstDayOfWeek,
            'long_date_format' => $this->longDateFormat,
            'short_date_format' => $this->shortDateFormat,
            'long_time_format' => $this->longTimeFormat,
            'short_time_format' => $this->shortTimeFormat,
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
