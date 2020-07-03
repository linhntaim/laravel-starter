<?php

namespace App\Jobs\Base;

use App\Utils\Traits\HomeSettingsHandleTrait;

abstract class HomeJob extends Job
{
    use HomeSettingsHandleTrait;
}
