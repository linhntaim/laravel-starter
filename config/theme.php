<?php

return [
    'default' => env('THEME_DEFAULT', App\Utils\Theme\Themes\Sample\Theme::NAME),
    'themes' => [
        App\Utils\Theme\Themes\Sample\Theme::NAME => App\Utils\Theme\Themes\Sample\Theme::class,
    ],
    'routes' => [

    ],
];
