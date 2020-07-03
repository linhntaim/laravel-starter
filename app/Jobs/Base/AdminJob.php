<?php

namespace App\Jobs\Base;

use App\Utils\Traits\AdminSettingsHandleTrait;

abstract class AdminJob extends Job
{
    use AdminSettingsHandleTrait;
}
