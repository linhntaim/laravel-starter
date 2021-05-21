<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\Notifications\TestMailNotification;

class MailNotificationCommand extends Command
{
    protected $signature = 'test:mail-notification {--subject=Tested} {--view=test}';

    protected function go()
    {
        (new TestMailNotification($this->option('subject'), $this->option('view')))->sendAny([
//            'mail' => 'dsquare.gbu@gmail.com',
        ]);
    }
}
