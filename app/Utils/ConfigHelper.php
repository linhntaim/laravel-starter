<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Configuration;
use App\Utils\ClientSettings\Facade;
use Symfony\Component\HttpFoundation\Response;

class ConfigHelper
{
    const NAME = 'starter';

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

    public static function getApiResponseStatus($defaultStatus = Response::HTTP_OK)
    {
        return static::get('api_response_ok') ?
            Response::HTTP_OK : $defaultStatus;
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

    /**
     * @param string|null $id
     * @return array
     */
    public static function getClientApp($id = null)
    {
        return is_null($id) ? static::get('client.apps') : static::get(sprintf('client.apps.%s', $id));
    }
}
