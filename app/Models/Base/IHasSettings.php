<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use App\Utils\ClientSettings\ISettings;

interface IHasSettings
{
    /**
     * @return ISettings
     */
    public function getSettings();
}
