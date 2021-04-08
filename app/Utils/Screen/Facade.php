<?php

namespace App\Utils\Screen;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Facade
 * @package App\Utils\Screen
 * @method static Manager setScreen($screen)
 * @method static array|null getScreen()
 * @method static array|null getScreens()
 * @method static null getScreenName()
 * @method static Manager fetchFromRequestHeader(Request $request)
 *
 * @see \App\Utils\Screen\Manager
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
