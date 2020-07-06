<?php

namespace App\Jobs\Base;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeJob extends Job
{
    use HomeSettingsHandleTrait;
}
