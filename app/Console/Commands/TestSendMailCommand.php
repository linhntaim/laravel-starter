<?php

namespace App\Console\Commands;

use App\Utils\Mail\MailHelper;

class TestSendMailCommand extends Command
{
    protected $signature = 'test:send-mail';

    protected function go()
    {
        MailHelper::sendTestMail();
    }
}
