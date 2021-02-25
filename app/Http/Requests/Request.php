<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Requests;

use App\Utils\Helper;
use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    use AdminRequestTrait, ImpersonateRequestTrait, MemberRequestTrait;

    public function input($key = null, $default = null)
    {
        if (is_null($key)) {
            return parent::input($key);
        }
        return Helper::default(parent::input($key), $default);
    }

    public function ifInput($key, &$input)
    {
        $input = parent::input($key);
        return !is_null($input);
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
        return parent::ajax() || in_array(
                'x-requested-with',
                array_map(
                    function ($item) {
                        return strtolower(trim($item));
                    },
                    explode(',', $this->headers->get('access-control-request-headers'))
                )
            );
    }

    public function expectsJson()
    {
        return parent::expectsJson() || $this->is('api/*');
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
