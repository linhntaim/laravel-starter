<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class OnQueryExecuted extends NowListener
{
    /**
     * @param QueryExecuted $event
     */
    protected function go($event)
    {
        if (config('app.debug')) {
            Log::info(
                sprintf(
                    'Time: %sms. SQL: %s. Bindings: %s. Connection: %s.',
                    $event->sql,
                    json_encode($event->bindings),
                    $event->time,
                    $event->connectionName
                )
            );
        }
    }
}
