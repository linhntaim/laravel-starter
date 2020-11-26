<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ExtraActions;

use Illuminate\Support\Facades\Facade;

/**
 * Class ActionHook
 * @package App\Utils\ExtraActions
 * @method static string register(callable $callback, string $namespace, $id = null)
 * @method static array activate(string $namespace, ...$params)
 */
class ActionHook extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return HookAction::class;
    }
}
