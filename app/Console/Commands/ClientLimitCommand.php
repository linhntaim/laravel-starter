<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;

class ClientLimitCommand extends Command
{
    protected $signature = 'client:limit {--none} {--allow=} {--deny=} {--admin}';

    protected function go()
    {
        $file = storage_path('framework/limit');

        if ($this->option('none')) {
            if (file_exists($file)) {
                unlink($file);
            }
        } else {
            $allowed = $this->option('allow');
            $denied = $this->option('deny');
            $admin = $this->option('admin');

            file_put_contents($file, json_encode([
                'allowed' => empty($allowed) ? [] : $allowed,
                'denied' => empty($denied) ? [] : $denied,
                'admin' => $admin ? true : false,
            ]));
        }
    }
}
