<?php

namespace App\Listeners;

use App\Utils\AppOptionHelper;
use App\Utils\ClassTrait;

abstract class NowListener
{
    use ClassTrait;

    protected static function __transListener($name, $replace = [], $locale = null)
    {
        return static::__transWithSpecificModule($name, 'listener', $replace, $locale);
    }

    protected static function __transWithAppInfoListener($name, $replace = [], $locale = null)
    {
        $appOptions = AppOptionHelper::getInstance();
        $replace = array_merge($replace, [
            'company_short_name' => $appOptions->getBy('company_short_name'),
        ]);
        return static::__transWithSpecificModule($name, 'listener', $replace, $locale);
    }
}
