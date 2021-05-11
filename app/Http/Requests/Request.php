<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Requests;

use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    use AdminRequestTrait, ImpersonateRequestTrait;

    public function ifHeader($key, &$header, $filled = false)
    {
        $header = is_null($key) ? null : parent::header($key);
        return has($header, $filled);
    }

    public function header($key = null, $default = null, $filled = true)
    {
        return is_null($key) ? parent::header($key) : got(parent::header($key), $default, $filled);
    }

    public function ifHeaderJson($key, &$header, $filled = false)
    {
        if ($this->ifHeader($key, $header, $filled)) {
            $header = jsonDecodeArray($header);
            return !empty($header);
        }
        return false;
    }

    public function headerJson($key, array $default = [], $filled = true)
    {
        $header = is_null($key) ? null : parent::header($key);
        return has($header, $filled) ? jsonDecodeArray($header) : $default;
    }

    public function ifCookie($key, &$cookie, $filled = false)
    {
        $cookie = is_null($key) ? null : parent::cookie($key);
        return has($cookie, $filled);
    }

    public function cookie($key = null, $default = null, $filled = true)
    {
        return is_null($key) ? parent::cookie($key) : got(parent::cookie($key), $default, $filled);
    }

    public function ifCookieJson($key, &$cookie, $filled = false)
    {
        if ($this->ifCookie($key, $cookie, $filled)) {
            $cookie = jsonDecodeArray($cookie);
            return !empty($cookie);
        }
        return false;
    }

    public function cookieJson($key, array $default = [], $filled = true)
    {
        $cookie = is_null($key) ? null : parent::cookie($key);
        return has($cookie, $filled) ? jsonDecodeArray($cookie) : $default;
    }

    public function ifInput($key, &$input, $filled = false)
    {
        $input = is_null($key) ? null : $this->input($key);
        return has($input, $filled);
    }

    public function input($key = null, $default = null, $filled = true)
    {
        return is_null($key) ? parent::input($key) : got(parent::input($key), $default, $filled);
    }

    public function ifFile($key, &$file)
    {
        $file = $this->file($key);
        return !is_null($file);
    }

    public function ajax()
    {
        return parent::ajax()
            || in_array(
                'x-requested-with',
                array_map(
                    function ($item) {
                        return strtolower(trim($item));
                    },
                    explode(',', $this->headers->get('Access-Control-Request-Headers'))
                )
            );
    }

    public function expectsJson()
    {
        return parent::expectsJson() || $this->is('api') || $this->is('api/*');
    }

    public function possiblyIs(...$patterns)
    {
        return $this->is(...$patterns)
            || $this->routeIs(...$patterns)
            || $this->fullUrlIs(...$patterns);
    }

    public static function normalizeQueryStringWithoutSorting($qs)
    {
        if ('' == $qs) {
            return '';
        }

        parse_str($qs, $qs);

        return http_build_query($qs, '', '&', PHP_QUERY_RFC3986);
    }
}
