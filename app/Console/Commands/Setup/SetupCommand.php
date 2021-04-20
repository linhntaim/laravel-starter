<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

class SetupCommand extends Command
{
    protected $signature = 'setup {--u} {--f} {--seed-dummy} {--seed-test} {--skip=*} {--only=*}';

    protected function skipped()
    {
        return $this->option('skip');
    }

    protected function only()
    {
        return $this->option('only');
    }

    protected function goInstalling()
    {
        $skipped = $this->skipped();
        $only = $this->only();
        $hasOnly = count($only) > 0;
        $forced = $this->forced();
        if (!in_array('web-server', $skipped) && (!$hasOnly || in_array('web-server', $only))) {
            $this->call('setup:web-server', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }
        if (!in_array('packages', $skipped) && (!$hasOnly || in_array('packages', $only))) {
            $this->call('setup:packages', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }
        if (!in_array('key:generate', $skipped) && (!$hasOnly || in_array('key:generate', $only))) {
            $this->call('setup:key:generate', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }
        if (!in_array('storage:link', $skipped) && (!$hasOnly || in_array('storage:link', $only))) {
            $this->call('setup:storage:link', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }
        if (!in_array('migrate', $skipped) && (!$hasOnly || in_array('migrate', $only))) {
            $this->call('setup:migrate', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }

        if ($this->option('seed-dummy')) {
            $this->call('setup:seed:dummy', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }

        if ($this->option('seed-test')) {
            $this->call('setup:seed:test', $forced ? [
                '--f' => true,
            ] : []);
            $this->newLine();
        }
    }

    protected function goUninstalling()
    {
        $this->call('setup:migrate', [
            '--u' => true,
        ]);
        $this->newLine();
        $this->call('setup:storage:link', [
            '--u' => true,
        ]);
        $this->newLine();
        $this->call('setup:key:generate', [
            '--u' => true,
        ]);
        $this->newLine();
        $this->call('setup:packages', [
            '--u' => true,
        ]);
        $this->newLine();
        $this->call('setup:web-server', [
            '--u' => true,
        ]);
        $this->newLine();

        if ($this->option('seed-dummy')) {
            $this->call('setup:seed:dummy', [
                '--u' => true,
            ]);
            $this->newLine();
        }

        if ($this->option('seed-test')) {
            $this->call('setup:seed:test', [
                '--u' => true,
            ]);
            $this->newLine();
        }
    }
}
