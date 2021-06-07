<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Vendors\Illuminate\Support\Facades\App;

trait ExecutionTimeTrait
{
    protected function resetExecutionTime()
    {
        if (App::runningFromRequest()) {
            set_time_limit(maxExecutionTime());
        }
    }
}