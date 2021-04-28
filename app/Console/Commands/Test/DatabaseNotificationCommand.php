<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Test;

use App\Console\Commands\Base\Command;
use App\ModelRepositories\AdminRepository;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\User;
use App\Notifications\TestDatabaseNotification;

class DatabaseNotificationCommand extends Command
{
    use ModelTransformTrait;

    protected $signature = 'test:notification:db {--test=test}';

    protected function go()
    {
        $this->info('Test notification sending...');
        $admin = (new AdminRepository())->getById(User::USER_ADMINISTRATOR_ID);
        (new TestDatabaseNotification($this->option('test')))->send($admin);
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
            $this->warn('[Transformed]');
            print_r($this->modelTransform($notification));
            if ($notification->read()) {
                $this->warn('Read at:');
                $this->line($notification->sdStReadAt);
            }
            $this->info('------------------------------------');
        }
        $this->info('Notifications listed!');
    }
}
