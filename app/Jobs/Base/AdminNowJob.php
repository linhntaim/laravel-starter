<?php

namespace App\Jobs\Base;

use App\Utils\Traits\AdminSettingsHandleTrait;

abstract class AdminNowJob extends NowJob
{
    use AdminSettingsHandleTrait;
}
