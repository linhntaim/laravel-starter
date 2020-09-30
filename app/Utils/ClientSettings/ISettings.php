<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

interface ISettings
{
    /**
     * @return string
     */
    public function getLocale();
}