<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs;

use App\Jobs\Base\Job;
use App\Utils\LogHelper;

class TestingJob extends Job
{
    public function go()
    {
        LogHelper::info(sprintf('%s executed', static::class));
    }

    public function failed()
    {
        LogHelper::info(sprintf('%s failed', static::class));
    }
}
