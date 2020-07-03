<?php

namespace App\Http\Requests;

use App\Utils\Helper;
use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    use AdminRequestTrait;

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

    public function ajax()
    {
        $accessControlRequestHeaders = explode(',', $this->headers->get('access-control-request-headers'));
        $accessControlRequestHeaders = array_map(function ($item) {
            return strtolower(trim($item));
        }, $accessControlRequestHeaders);
        return parent::ajax() || in_array('x-requested-with', $accessControlRequestHeaders);
    }

    public function expectsJson()
    {
        return parent::expectsJson() || $this->is('api/*');
    }

    public static function normalizeQueryStringWithoutSort($qs)
    {
        if ('' == $qs) {
            return '';
        }

        parse_str($qs, $qs);

        return http_build_query($qs, '', '&', PHP_QUERY_RFC3986);
    }
}
