<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings\Traits;

use App\Utils\ClientSettings\Facade;

trait IndependentClientTrait
{
    public function independentClientId()
    {
        return null;
    }

    public function independentClientApply($clientId = null)
    {
        if (is_null($clientId)) {
            $clientId = $this->independentClientId();
        }
        if ($clientId) {
            Facade::setClient($clientId);
        }
    }
}