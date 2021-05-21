<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Mail\Base\MailTrait;
use App\Mail\TestMailable;

class MailCommand extends Command
{
    use MailTrait;

    protected $signature = 'test:mail {--subject=Tested} {--view=test}';

    protected function go()
    {
        $this->mail(
            new TestMailable($this->option('subject'), $this->option('view'))
        );
    }
}
