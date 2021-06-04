<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\ModelRepositories\AdminRepository;
use App\Models\User;
use App\Notifications\TestNotification;

class NotificationCommand extends Command
{
    protected $signature = 'test:notification';

    protected function go()
    {
        $admin = (new AdminRepository())->getById(User::USER_ADMINISTRATOR_ID);
        (new TestNotification())->send($admin);
    }
}
