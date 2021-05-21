<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Mail\TestMailable;
use Illuminate\Support\Facades\Mail;

class MailCommand extends Command
{
    protected $signature = 'test:mail {--subject=Tested} {--view=test}';

    protected function go()
    {
        Mail::send(
            new TestMailable($this->option('subject'), $this->option('view'))
        );
    }
}
