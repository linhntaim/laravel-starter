<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs;

use App\Jobs\Base\Job;
use Illuminate\Support\Facades\Log;

class TestingJob extends Job
{
    public function go()
    {
        Log::info(sprintf('%s executed', static::class));
    }

    public function failed()
    {
        Log::info(sprintf('%s failed', static::class));
    }
}
