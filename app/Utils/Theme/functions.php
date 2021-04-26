<?php

function themeAsset($path, $secure = null)
{
    return App\Utils\Theme\ThemeFacade::asset($path, $secure);
}

function themeTitle($titles = null, $complement = null)
{
    return App\Utils\Theme\ThemeFacade::title($titles, $complement);
}

function __t($key, array $replace = [], $locale = null)
{
    return App\Utils\Theme\ThemeFacade::trans($key, $replace, false, $locale);
}

function __tc($key, $number, array $replace = [], $locale = null)
{
    return App\Utils\Theme\ThemeFacade::transChoice($key, $number, $replace, false, $locale);
}

function __ct($key, array $replace = [], $locale = null)
{
    return App\Utils\Theme\ThemeFacade::trans($key, $replace, true, $locale);
}

function __ctc($key, $number, array $replace = [], $locale = null)
{
    return App\Utils\Theme\ThemeFacade::transChoice($key, $number, $replace, false, $locale);
}
