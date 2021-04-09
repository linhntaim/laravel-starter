<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\Utils\ClientSettings\Traits\AdminConsoleClientTrait;

abstract class AdminCommand extends Command
{
    use AdminConsoleClientTrait;
}
