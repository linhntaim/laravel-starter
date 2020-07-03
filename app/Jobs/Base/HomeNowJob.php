<?php

namespace App\Jobs\Base;

use App\Utils\Traits\HomeSettingsHandleTrait;

abstract class HomeNowJob extends NowJob
{
    use HomeSettingsHandleTrait;
}
