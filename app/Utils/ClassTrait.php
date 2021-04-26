<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Configuration;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait ClassTrait
{
    protected static $__transNamespace = '';

    protected static $__transNamespaceFallback = false;

    protected static function __setTransNamespace($transNamespace, $fallback = false)
    {
        static::$__transNamespace = $transNamespace . '::';
        static::$__transNamespaceFallback = $fallback;
    }

    protected static function __class()
    {
        return static::class;
    }

    protected static function __classBaseName()
    {
        return class_basename(static::__class());
    }

    protected static function __snakyClassBaseName()
    {
        return Str::snake(static::__classBaseName());
    }

    protected static function __friendlyClassBaseName()
    {
        return Str::title(Str::snake(static::__classBaseName(), ' '));
    }

    protected static function __transWithTemporaryNamespace($transNamespace, callable $transCallback, $fallback = false)
    {
        $origin = static::$__transNamespace;
        $originFallback = static::$__transNamespaceFallback;
        static::__setTransNamespace($transNamespace, $fallback);
        $trans = $transCallback();
        static::__setTransNamespace($origin, $originFallback);
        return $trans;
    }

    protected static function __transCurrentModule()
    {
        return null;
    }

    protected static function __hasTrans($key, $locale = null, $fallback = true)
    {
        $key = sprintf('%s%s', static::$__transNamespace, $key);
        return Lang::has($key, $locale, $fallback);
    }

    protected static function __hasTransWithoutNamespace($key, $locale = null, $fallback = true)
    {
        return Lang::has($key, $locale, $fallback);
    }

    protected static function __trans($key, $replace = [], $locale = null)
    {
        if (static::$__transNamespace && static::$__transNamespaceFallback) {
            if (static::__hasTransWithoutNamespace($namespacedKey = sprintf('%s%s', static::$__transNamespace, $key), $locale)) {
                $key = $namespacedKey;
            }
        } else {
            $key = sprintf('%s%s', static::$__transNamespace, $key);
        }
        return trans($key, $replace, $locale);
    }

    protected static function __transChoice($key, $number, $replace = [], $locale = null)
    {
        if (static::$__transNamespace && static::$__transNamespaceFallback) {
            if (static::__hasTransWithoutNamespace($namespacedKey = sprintf('%s%s', static::$__transNamespace, $key), $locale)) {
                $key = $namespacedKey;
            }
        } else {
            $key = sprintf('%s%s', static::$__transNamespace, $key);
        }
        return trans_choice($key, $number, $replace, $locale);
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
        return static::__hasTrans(static::__transPathWithModule($name, $module, true), $locale, $fallback);
    }

    protected static function __transWithSpecificModule($name, $module, $replace = [], $locale = null)
    {
        return static::__trans(static::__transPathWithModule($name, $module, true), $replace, $locale);
    }

    protected static function __hasTransErrorWithModule($error, $locale = null, $fallback = true)
    {
        return static::__hasTransWithModule($error, 'error', $locale, $fallback);
    }

    protected static function __transMailWithModule($mail, $replace = [], $locale = null)
    {
        return static::__transWithModule($mail, 'mail', $replace, $locale);
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
        return static::__hasTrans(static::__transPathWithModule($name, $module), $locale, $fallback);
    }

    protected static function __transWithModule($name, $module, $replace = [], $locale = null)
    {
        return static::__trans(static::__transPathWithModule($name, $module), $replace, $locale);
    }

    protected static function __transPathWithModule($name, $module, $specific = false)
    {
        $classNameKey = $specific ?
            static::__snakyClassBaseName()
            : (function ($namespacedClassName) {
                $classNames = explode('\\', str_replace(Configuration::ROOT_NAMESPACE . '\\', '', $namespacedClassName));
                foreach ($classNames as &$className) {
                    $className = Str::snake($className);
                }
                return implode('.', $classNames);
            })(static::class);
        return sprintf(
            '%s.%s.%s',
            $module,
            $classNameKey,
            $name
        );
    }

    protected static function __transError($error, $replace = [], $locale = null)
    {
        return static::__trans(static::__transErrorPath($error), $replace, $locale);
    }

    protected static function __transErrorPath($error)
    {
        return sprintf('%s.%s', 'error', $error);
    }
}
