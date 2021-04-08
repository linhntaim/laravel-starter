<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\Utils\ClientSettings\AdminClientTrait;

abstract class AdminCommand extends Command
{
    use AdminClientTrait;
}
