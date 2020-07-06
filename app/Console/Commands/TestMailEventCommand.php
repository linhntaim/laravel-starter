<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Events\MailTestingEvent;

class TestMailEventCommand extends Command
{
    protected $signature = 'test:mail-event';

    protected function go()
    {
        event(new MailTestingEvent());
    }
}
