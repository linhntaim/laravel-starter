<?php

namespace App\Jobs\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminJob extends Job
{
    use AdminSettingsHandleTrait;
}
