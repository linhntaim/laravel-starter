<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings\Traits;

use App\Vendors\Illuminate\Support\Facades\App;

trait ConsoleClientTrait
{
    use IndependentClientTrait;

    public function consoleClientId()
    {
        return $this->independentClientId();
    }

    public function consoleClientApply()
    {
        if (App::notRunningFromRequest()) {
            $this->independentClientApply($this->consoleClientId());
        }
    }
}