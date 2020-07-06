<?php

namespace App\Utils;

use App\Configuration;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait ClassTrait
{
    protected static $__transNamespace = '';

    protected static $__friendlyClassBaseName;

    protected static $__snakyClassBaseName;

    protected static function setTransNamespace($transNamespace)
    {
        static::$__transNamespace = $transNamespace . '::';
    }

    protected static function __class()
    {
        return static::class;
    }

    protected static function __classBaseName()
    {
        return class_basename(static::class);
    }

    protected static function __snakyClassBaseName()
    {
        if (empty(static::$__snakyClassBaseName)) {
            static::$__snakyClassBaseName = Str::snake(static::__classBaseName());
        }
        return static::$__snakyClassBaseName;
    }

    protected static function __friendlyClassBaseName()
    {
        if (empty(static::$__friendlyClassBaseName)) {
            static::$__friendlyClassBaseName = Str::title(Str::snake(static::__classBaseName(), ' '));
        }
        return static::$__friendlyClassBaseName;
    }

    protected static function __transCurrentModule()
    {
        return null;
    }

    protected static function __trans($key = null, $replace = [], $locale = null)
    {
        $key = sprintf('%s%s', static::$__transNamespace, $key);
        return trans(empty($key) ? null : $key, $replace, $locale);
    }

    protected static function __transChoice($key, $number, $replace = [], $locale = null)
    {
        $key = sprintf('%s%s', static::$__transNamespace, $key);
        return trans_choice(empty($key) ? null : $key, $number, $replace, $locale);
    }

    protected static function __hasTransWithCurrentModule($name, $locale = null, $fallback = true)
    {
        return static::__hasTransWithSpecificModule($name, static::__transCurrentModule(), $locale, $fallback);
    }

    protected static function __transWithCurrentModule($name, $replace = [], $locale = null)
    {
        return static::__transWithSpecificModule($name, static::__transCurrentModule(), $replace, $locale);
    }

    protected static function __hasTransWithSpecificModule($name, $module, $locale = null, $fallback = true)
    {
        return Lang::has(static::__transPathWithModule($name, $module, true), $locale, $fallback);
    }

    protected static function __transWithSpecificModule($name, $module, $replace = [], $locale = null)
    {
        return trans(static::__transPathWithModule($name, $module, true), $replace, $locale);
    }

    protected static function __hasTransErrorWithModule($error, $locale = null, $fallback = true)
    {
        return static::__hasTransWithModule($error, 'error', $locale, $fallback);
    }

    protected static function __transErrorWithModule($error, $replace = [], $locale = null)
    {
        return static::__transWithModule($error, 'error', $replace, $locale);
    }

    protected static function __transErrorPathWithModule($error)
    {
        return static::__transPathWithModule($error, 'error');
    }

    protected static function __hasTransWithModule($name, $module, $locale = null, $fallback = true)
    {
        return Lang::has(static::__transPathWithModule($name, $module), $locale, $fallback);
    }

    protected static function __transWithModule($name, $module, $replace = [], $locale = null)
    {
        return trans(static::__transPathWithModule($name, $module), $replace, $locale);
    }

    protected static function __transPathWithModule($name, $module, $specific = false)
    {
        if ($specific) {
            return sprintf('%s%s.%s.%s', static::$__transNamespace, $module, static::__snakyClassBaseName(), $name);
        }

        $classNames = explode('\\', str_replace(Configuration::ROOT_NAMESPACE . '\\', '', static::class));
        foreach ($classNames as &$className) {
            $className = Str::snake($className);
        }
        return sprintf('%s%s.%s.%s', static::$__transNamespace, $module, implode('.', $classNames), $name);
    }

    protected static function __transError($error, $replace = [], $locale = null)
    {
        return trans(static::__transErrorPath($error), $replace, $locale);
    }

    protected static function __transErrorPath($error)
    {
        return sprintf('%s%s.%s', static::$__transNamespace, 'error', $error);
    }
}
