<?php

namespace App\Console\Commands\Base;

use App\Utils\Traits\AdminSettingsHandleTrait;

abstract class AdminCommand extends Command
{
    use AdminSettingsHandleTrait;
}
