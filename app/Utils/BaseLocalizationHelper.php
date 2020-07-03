<?php

namespace App\Utils;

use App\Configuration;
use App\ModelRepositories\UserRepository;
use App\ModelTraits\IUser;

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

    /**
     * @return static
     */
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
        if (!is_null($timezone) && in_array($timezone, DateTimer::getTimezoneValues())) {
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
        if (!is_null($firstDayOfWeek) && in_array($firstDayOfWeek, DateTimer::getDaysOfWeekValues())) {
            $this->firstDayOfWeek = $firstDayOfWeek;
        }
    }

    public function getFirstDayOfWeek()
    {
        return $this->firstDayOfWeek;
    }

    public function setLongDateFormat($longDateFormat)
    {
        if (!is_null($longDateFormat) && in_array($longDateFormat, DateTimer::getLongDateFormatValues())) {
            $this->longDateFormat = $longDateFormat;
        }
    }

    public function getLongDateFormat()
    {
        return $this->longDateFormat;
    }

    public function setShortDateFormat($shortDateFormat)
    {
        if (!is_null($shortDateFormat) && in_array($shortDateFormat, DateTimer::getShortDateFormatValues())) {
            $this->shortDateFormat = $shortDateFormat;
        }
    }

    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    public function setLongTimeFormat($longTimeFormat)
    {
        if (!is_null($longTimeFormat) && in_array($longTimeFormat, DateTimer::getLongTimeFormatValues())) {
            $this->longTimeFormat = $longTimeFormat;
        }
    }

    public function getLongTimeFormat()
    {
        return $this->longTimeFormat;
    }

    public function setShortTimeFormat($shortTimeFormat)
    {
        if (!is_null($shortTimeFormat) && DateTimer::getShortTimeFormatValues()) {
            $this->shortTimeFormat = $shortTimeFormat;
        }
    }

    public function getShortTimeFormat()
    {
        return $this->shortTimeFormat;
    }

    /**
     * @param IUser|integer|null $user
     * @return static
     */
    public function autoFetch($user = null)
    {
        $user = is_null($user) ? null : (new UserRepository())->model($user);
        if (!is_null($user)) {
            $this->fetchFromUser($user);
        } elseif (app()->runningInConsole()) {
            $this->fetchFromClient(Configuration::$currentClient);
        } else {
            $this->fetchFromRequestHeader();
        }
        return $this->apply();
    }

    public function fetchFromArray($localization)
    {
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
        return $this;
    }

    public function fetchFromClient($clientAppId)
    {
        if (empty($clientAppId)) return $this;

        $client = ConfigHelper::getClient($clientAppId);
        return $this->fetchFromArray($client['default_localization']);
    }

    public function fetchFromRequestHeader()
    {
        $request = request();
        if (!$request->headers->has('Localization')) return $this;

        $localization = json_decode($request->header('Localization'), true);
        if ($localization === false) return $this;

        if (isset($localization['_from_app'])) $this->fromApp = true;
        if (isset($localization['_ts'])) $this->setTimestamp($localization['_ts']);
        return $this->fetchFromArray($localization);
    }

    /**
     * @param IUser $user
     * @return static
     */
    public function fetchFromUser($user)
    {
        return $this->fetchFromArray($user->preferredLocalization());
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
