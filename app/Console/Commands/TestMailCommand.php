<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Utils\Mail\MailHelper;

class TestMailCommand extends Command
{
    protected $signature = 'test:mail';

    protected function go()
    {
        MailHelper::sendTestMail();
    }
}
