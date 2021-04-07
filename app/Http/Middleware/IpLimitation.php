<?php

namespace App\Http\Middleware;

use App\Exceptions\IpLimitException;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\IpLimiter;
use Closure;

class IpLimitation
{
    protected $except = [
        // TODO:

        // TODO
    ];

    public function handle(Request $request, Closure $next, $group = null)
    {
        $limit = ConfigHelper::get('ip_limit', []);
        $allowed = [];
        $denied = [];
        $except = $this->except;
        if (is_null($group)) {
            $this->getLimit($limit, $allowed, $denied);
        } else {
            if (isset($limit[$group])) {
                $this->getLimit($limit[$group], $allowed, $denied);
                if (isset($this->except[$group])) {
                    $except = $this->except[$group];
                }
            }
        }
        if (!(new IpLimiter($allowed, $denied))->canAccess($request, $except)) {
            throw new IpLimitException();
        }
        return $next($request);
    }

    protected function getLimit($limit, &$allowed, &$denied)
    {
        if (isset($limit['allowed'])) {
            $allowed = $limit['allowed'];
        }
        if (isset($limit['denied'])) {
            $denied = $limit['denied'];
        }
    }
}