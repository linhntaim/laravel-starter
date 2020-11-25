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
    protected $signature = 'test:notification {--test=test}';

    protected function go()
    {
        $this->info('Test notification sending...');
        $admin = (new AdminRepository())->getById(User::USER_ADMINISTRATOR_ID);
        (new TestNotification($this->option('test')))->send($admin);
        $this->info('Test notification sent!');

        $this->info('Notifications listing...');
        foreach ($admin->notifications as $notification) {
            $this->info('------------------------------------');
            $this->warn('Title:');
            $this->line($notification->title);
            $this->warn('Content:');
            $this->line($notification->content);
            $this->warn('Created at:');
            $this->line($notification->sdStCreatedAt);
            if ($notification->read()) {
                $this->warn('Read at:');
                $this->line($notification->sdStReadAt);
            }
            $this->info('------------------------------------');
        }
        $this->info('Notifications listed!');
    }
}
