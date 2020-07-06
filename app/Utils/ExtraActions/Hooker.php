<?php

namespace App\Utils\ExtraActions;

use Illuminate\Support\Facades\Facade;

/**
 * Class Hooker
 * @package App\Utils\ExtraActions
 * @method static int add(string $name, callable $callback)
 * @method static array activate(string $name, ...$params)
 */
class Hooker extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return HookExtraAction::class;
    }
}
