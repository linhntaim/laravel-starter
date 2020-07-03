<?php

namespace App\Console\Commands\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminCommand extends Command
{
    use AdminSettingsHandleTrait;
}
