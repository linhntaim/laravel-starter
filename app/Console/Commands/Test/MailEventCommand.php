<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Events\MailTestingEvent;

class MailEventCommand extends Command
{
    protected $signature = 'test:mail-event {--subject=Tested}';

    protected function go()
    {
        event(new MailTestingEvent($this->option('subject')));
    }
}
