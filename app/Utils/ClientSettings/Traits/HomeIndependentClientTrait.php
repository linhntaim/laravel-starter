<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings\Traits;

trait HomeIndependentClientTrait
{
    public function independentClientId()
    {
        return 'home';
    }
}