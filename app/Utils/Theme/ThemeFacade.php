<?php

namespace App\Utils\Theme;

use Illuminate\Support\Facades\Facade;

/**
 * Class ThemeFacade
 * @package App\Utils\Theme
 * @method static string getName()
 * @method static string getDisplayName()
 * @method static string getAppType()
 * @method static array share()
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory view($view, $data = [], $mergeData = [])
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory pageView($view, $data = [], $mergeData = [])
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|mixed pageHome($data = [], $mergeData = [], $view = 'welcome')
 * @method static string pageViewPath($view)
 * @method static string viewPath($view)
 * @method static boolean viewExists($view)
 * @method static boolean pageViewExists($view)
 * @method static string asset($path, $secure = null)
 */
class ThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Theme::class;
    }
}
