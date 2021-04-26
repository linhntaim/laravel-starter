<?php

namespace App\Utils\Theme;

use Illuminate\Support\Facades\Facade;

/**
 * Class ThemeFacade
 * @package App\Utils\Theme
 * @method static Theme instance()
 * @method static string getName()
 * @method static string getDisplayName()
 * @method static array share()
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory view($view, $data = [], $mergeData = [])
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory pageView($view, $data = [], $mergeData = [])
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|mixed pageHome($data = [], $mergeData = [], $view = 'welcome')
 * @method static string pageViewPath($view)
 * @method static string viewPath($view)
 * @method static boolean viewExists($view)
 * @method static boolean pageViewExists($view)
 * @method static string asset($path, $secure = null)
 * @method static string title($titles = null, $complement = null)
 * @method static string trans($key, array $replace = [], $custom = false, $locale = null)
 * @method static string transChoice($key, $number, array $replace = [], $custom = false, $locale = null)
 */
class ThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Theme::class;
    }
}
