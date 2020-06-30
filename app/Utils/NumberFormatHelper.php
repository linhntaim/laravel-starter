<?php

namespace App\Utils;

use App\Models\User;

class NumberFormatHelper extends BaseNumberFormatHelper
{
    /**
     * @param integer|User $user
     * @return NumberFormatHelper
     */
    public static function fromUser($user)
    {
        return new static((new LocalizationHelper())->fetchFromUser($user));
    }
}
