<?php

namespace App\Http\Middleware\Web;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Facade;
use Closure;

class Locale
{
    protected $params = [
        'locale',
        'lang',
    ];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->params as $param) {
            if ($request->ifInput($param, $locale)
                && filled($locale)) {
                $this->updateLocale($locale);
                break;
            }
        }
        return $next($request);
    }

    protected function updateLocale($locale)
    {
        Facade::update([
            'locale' => $locale,
        ])->storeCookie();
    }
}
