<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs\Base;

use App\Utils\ClientSettings\AdminSettingsHandleTrait;

abstract class AdminJob extends Job
{
    use AdminSettingsHandleTrait;
}
