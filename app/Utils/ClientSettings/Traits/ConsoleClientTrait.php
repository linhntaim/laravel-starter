<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings\Traits;

trait ConsoleClientTrait
{
    use IndependentClientTrait;

    public function consoleClientId()
    {
        return $this->independentClientId();
    }

    public function consoleClientApply()
    {
        if (app()->runningInConsole() || app()->runningUnitTests()) {
            $this->independentClientApply($this->consoleClientId());
        }
    }
}