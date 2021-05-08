<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Requests;

use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    use AdminRequestTrait, ImpersonateRequestTrait;

    public function ifHeader($key, &$header)
    {
        $header = is_null($key) ? null : parent::header($key);
        return !is_null($header);
    }

    public function ifHeaderJson($key, &$header)
    {
        if ($this->ifHeader($key, $header)) {
            $header = json_decode($header, true);
            return !empty($header);
        }
        return false;
    }

    public function ifCookie($key, &$cookie)
    {
        $cookie = is_null($key) ? null : parent::cookie($key);
        return !is_null($cookie);
    }

    public function ifCookieJson($key, &$cookie)
    {
        if ($this->ifCookie($key, $cookie)) {
            $cookie = json_decode($cookie, true);
            return !empty($cookie);
        }
        return false;
    }

    public function ifInput($key, &$input)
    {
        $input = is_null($key) ? null : parent::input($key);
        return !is_null($input);
    }

    public function input($key = null, $default = null)
    {
        if (is_null($key)) {
            return parent::input($key);
        }
        return got(parent::input($key), $default);
    }

    public function ifInputNotEmpty($key, &$input)
    {
        return $this->ifInput($key, $input) && !empty($input);
    }

    public function ifFile($key, &$file)
    {
        $file = parent::file($key);
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
