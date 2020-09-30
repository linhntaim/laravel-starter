<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminCommand extends Command
{
    use AdminSettingsHandleTrait;
}
