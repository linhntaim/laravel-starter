<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ExtraActions;

use Illuminate\Support\Facades\Facade;

/**
 * Class ActionReplace
 * @package App\Utils\ExtraActions
 * @method static string register(callable $callback, string $namespace, $id = null)
 * @method static ReplaceAction setConditionCallback(string $namespace, $conditionCallback = true)
 * @method static ReplaceAction setDefaultCallback(string $namespace, callable $defaultCallback = null)
 * @method static array activate(string $namespace, ...$params)
 */
class ActionReplace extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ReplaceAction::class;
    }
}
