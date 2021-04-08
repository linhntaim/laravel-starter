<?php

namespace App\Utils\Theme;

use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ThemeHelper
{
    const REQUEST_PARAM_THEME = '_x_theme';

    public static function config($key, $default = null)
    {
        return config('theme.' . $key, $default);
    }

    /**
     * @return array
     * @throws
     */
    public static function themes()
    {
        $themeDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'Themes';
        $themes = [];
        foreach ((new Finder)->in($themeDirectory)->depth('< 1')->directories() as $directory) {
            $themeClass = __NAMESPACE__ . '\\Themes\\' . $directory->getBasename() . '\\Theme';
            if (class_exists($themeClass)) {
                $themes[(new ReflectionClass($themeClass))->getConstant('NAME')] = $themeClass;
            }
        }
        return $themes;
    }

    /**
     * @return array
     */
    public static function routeBasedThemes()
    {
        return static::config('routes');
    }

    /**
     * @return string
     */
    public static function defaultTheme()
    {
        return static::config('default');
    }
}
