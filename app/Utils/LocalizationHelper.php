<?php

namespace App\Utils;

use App\Models\User;

class LocalizationHelper extends BaseLocalizationHelper
{
    /**
     * @param integer|User|null $userId
     * @return LocalizationHelper
     */
    public function fetchFromUser($userId = null)
    {
        return $this->fetchFromRequestHeader();
    }
}
