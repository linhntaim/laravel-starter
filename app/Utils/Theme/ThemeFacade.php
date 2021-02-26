<?php

namespace App\Utils\Theme;

use Illuminate\Support\Facades\Facade;

/**
 * Class ThemeFacade
 * @package App\Utils\Theme
 * @method static string getName()
 */
class ThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Theme::class;
    }
}
