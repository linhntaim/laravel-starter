<?php

namespace App\Console\Commands\Base;

use App\Utils\ClientSettings\HomeSettingsHandleTrait;

abstract class HomeCommand extends Command
{
    use HomeSettingsHandleTrait;
}
