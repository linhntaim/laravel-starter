<?php

namespace App\Utils;

use App\Configuration;

class ConfigHelper
{
    const NAME = 'dsquare';

    protected static $socialLoginEnabled = null;

    public static function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return config(static::NAME);
        }
        if (is_array($key)) {
            $keyTemp = [];
            foreach ($key as $k => $v) {
                $keyTemp[static::NAME . '.' . $k] = $v;
            }
            return config($keyTemp);
        }
        return config(static::NAME . '.' . $key, $default);
    }

    public static function set($key, $value)
    {
        config()->set(static::NAME . '.' . $key, $value);
    }

    public static function getAppVersion()
    {
        return static::get('app.version');
    }

    public static function getNoReplyMail()
    {
        return static::get('emails.no_reply');
    }

    public static function getTestedMail()
    {
        return static::get('emails.tested');
    }

    public static function getApiResponseStatus($defaultStatus = Configuration::HTTP_RESPONSE_STATUS_OK)
    {
        return static::get('api_response_ok') ?
            Configuration::HTTP_RESPONSE_STATUS_OK : $defaultStatus;
    }

    public static function getApiResponseHeaders($headers = [])
    {
        $defaultHeaders = static::get('api_response_headers', []);
        return array_merge($defaultHeaders, $headers);
    }

    public static function getCurrentLocale()
    {
        return app()->getLocale();
    }

    public static function setCurrentLocale($locale)
    {
        app()->setLocale($locale);
    }

    public static function getLocaleCodes()
    {
        return static::get('locales');
    }

    public static function getCountries()
    {
        return static::get('countries');
    }

    public static function getCountryCodes()
    {
        return array_keys(static::getCountries());
    }

    public static function getCurrencies()
    {
        return static::get('currencies');
    }

    public static function getCurrencyCodes()
    {
        return array_keys(static::getCurrencies());
    }

    public static function getNumberFormats()
    {
        return static::get('number_formats');
    }

    public static function getClockBlock($secondRange = Configuration::CLOCK_BLOCK_RANGE)
    {
        return floor(time() / $secondRange);
    }

    public static function getClockBlockKey($callback = '', $secondRange = Configuration::CLOCK_BLOCK_RANGE)
    {
        $blockKey = Configuration::CLOCK_BLOCK_KEYS[static::getClockBlock($secondRange) % count(Configuration::CLOCK_BLOCK_KEYS)];
        return !empty($callback) ? $callback($blockKey) : $blockKey;
    }

    public static function getClient($name = null)
    {
        return empty($name) ? static::get('clients') : static::get('clients.' . $name);
    }
}
