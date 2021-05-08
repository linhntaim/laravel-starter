<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Utils\Framework\ClientLimiter;

class ClientLimitCommand extends Command
{
    protected $signature = 'client:limit {--u} {--allow=} {--deny=} {--admin}';

    protected function go()
    {
        $clientLimiter = new ClientLimiter();

        if ($this->option('u')) {
            $clientLimiter->remove();
        } else {
            $allowed = $this->option('allow');
            $denied = $this->option('deny');
            $admin = $this->option('admin');

            $clientLimiter->setAllowed(empty($allowed) ? [] : $allowed)
                ->setDenied(empty($denied) ? [] : $denied)
                ->setAdmin((bool)$admin)
                ->save();
        }
    }
}
