<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Utils\Mail\MailHelper;

class MailCommand extends Command
{
    protected $signature = 'test:mail';

    protected function go()
    {
        MailHelper::sendTestMailNow();
    }
}
