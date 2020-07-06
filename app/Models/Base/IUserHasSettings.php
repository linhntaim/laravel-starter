<?php

namespace App\Models\Base;

use App\Utils\ClientSettings\ISettings;

interface IUserHasSettings
{
    /**
     * @return ISettings
     */
    public function getSettings();
}
