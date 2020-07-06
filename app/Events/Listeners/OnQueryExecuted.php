<?php


namespace App\Events\Listeners;

use App\Events\Listeners\Base\NowListener;
use App\Utils\LogHelper;
use Illuminate\Database\Events\QueryExecuted;

class OnQueryExecuted extends NowListener
{
    /**
     * @param QueryExecuted $event
     */
    protected function go($event)
    {
        if (config('app.debug')) {
            LogHelper::info(sprintf('SQL: %s. Bindings: %s', $event->sql, json_encode($event->bindings)));
        }
    }
}
