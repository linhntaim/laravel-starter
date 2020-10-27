<?php

namespace App\Console\Commands\Setup;

class SetupCommand extends Command
{
    protected $signature = 'setup {--u} {--force}';

    protected function goInstalling()
    {
        $this->call('setup:web-server');
        $this->lineBreak();
        $this->call('setup:key:generate');
        $this->lineBreak();
        $this->call('setup:storage:link');
        $this->lineBreak();
    }

    protected function goUninstalling()
    {
        $this->call('setup:storage:link', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:key:generate', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:web-server', [
            '--u' => true,
        ]);
        $this->lineBreak();
    }
}