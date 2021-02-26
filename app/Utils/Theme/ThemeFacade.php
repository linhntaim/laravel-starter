<?php

namespace App\Utils\Theme;

use Illuminate\Support\Facades\Facade;

/**
 * Class ThemeFacade
 * @package App\Utils\Theme
 * @method static string getName()
 * @method static string getDisplayName()
 * @method static array share()
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory view($view, $data = [], $mergeData = [])
 */
class ThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Theme::class;
    }
}
