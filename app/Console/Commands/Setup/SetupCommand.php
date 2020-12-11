<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

class SetupCommand extends Command
{
    protected $signature = 'setup {--u} {--f} {--seed-dummy} {--seed-test} {--skip=*}';

    protected function skipped()
    {
        return $this->option('skip');
    }

    protected function goInstalling()
    {
        $skipped = $this->skipped();
        $forced = $this->forced();
        if (!in_array('web-server', $skipped)) {
            $this->call('setup:web-server', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }
        if (!in_array('packages', $skipped)) {
            $this->call('setup:packages', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }
        if (!in_array('key:generate', $skipped)) {
            $this->call('setup:key:generate', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }
        if (!in_array('storage:link', $skipped)) {
            $this->call('setup:storage:link', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }
        if (!in_array('migrate', $skipped)) {
            $this->call('setup:migrate', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }

        if ($this->option('seed-dummy')) {
            $this->call('setup:seed:dummy', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }

        if ($this->option('seed-test')) {
            $this->call('setup:seed:test', $forced ? [
                '--f' => true,
            ] : []);
            $this->lineBreak();
        }
    }

    protected function goUninstalling()
    {
        $this->call('setup:migrate', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:storage:link', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:key:generate', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:packages', [
            '--u' => true,
        ]);
        $this->lineBreak();
        $this->call('setup:web-server', [
            '--u' => true,
        ]);
        $this->lineBreak();

        if ($this->option('seed-dummy')) {
            $this->call('setup:seed:dummy', [
                '--u' => true,
            ]);
            $this->lineBreak();
        }

        if ($this->option('seed-test')) {
            $this->call('setup:seed:test', [
                '--u' => true,
            ]);
            $this->lineBreak();
        }
    }
}
