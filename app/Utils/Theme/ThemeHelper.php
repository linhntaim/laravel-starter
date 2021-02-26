<?php

namespace App\Utils\Theme;

class ThemeHelper
{
    const REQUEST_PARAM_THEME = '_x_theme';

    public static function config($key, $default = null)
    {
        return config('theme.' . $key, $default);
    }

    /**
     * @return array
     */
    public static function themes()
    {
        return static::config('themes');
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
