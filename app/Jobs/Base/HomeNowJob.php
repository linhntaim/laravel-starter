<?php

namespace App\Jobs\Base;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeNowJob extends NowJob
{
    use HomeSettingsHandleTrait;
}
