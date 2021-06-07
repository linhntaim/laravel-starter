<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

use Illuminate\Contracts\Translation\HasLocalePreference;

interface ILocalizable extends HasLocalePreference
{
    public function preferredSettings();
}