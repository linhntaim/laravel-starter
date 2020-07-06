<?php

namespace App\Jobs\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminNowJob extends NowJob
{
    use AdminSettingsHandleTrait;
}
