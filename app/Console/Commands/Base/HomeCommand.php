<?php

namespace App\Console\Commands\Base;

use App\Utils\Traits\HomeSettingsHandleTrait;

abstract class HomeCommand extends Command
{
    use HomeSettingsHandleTrait;
}
