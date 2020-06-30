<?php

namespace App\Utils;

use App\Models\User;

class DateTimeHelper extends BaseDateTimeHelper
{
    /**
     * @param integer|User $user
     * @return DateTimeHelper
     */
    public static function fromUser(User $user)
    {
        return new static((new LocalizationHelper())->fetchFromUser($user));
    }
}
