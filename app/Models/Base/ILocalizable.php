<?php

namespace App\Models\Base;

use Illuminate\Contracts\Translation\HasLocalePreference;

interface ILocalizable extends HasLocalePreference
{
    public function preferredSettings();
}