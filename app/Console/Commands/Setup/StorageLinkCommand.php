<?php

namespace App\Console\Commands\Setup;

class StorageLinkCommand extends Command
{
    protected $signature = 'setup:storage:link {--u}';

    protected function goInstalling()
    {
        $links = $this->links(array_filter($this->links(), function ($link) {
            return !file_exists($link);
        }, ARRAY_FILTER_USE_KEY));

        if (!empty($links)) {
            $this->call('storage:link');

            // Sometimes, symlinks cannot be created on Windows, so we should create them as directories
            foreach ($links as $link => $target) {
                if (!file_exists($link)) {
                    if (false === @mkdir($link, 0777)) {
                        $this->error(sprintf('Cannot create [%s] link.', $link));
                    }
                }
            }
        }
    }

    protected function goUninstalling()
    {
        foreach ($this->links() as $link => $target) {
            if (file_exists($link)) {
                if (is_link($link)) unlink($link);
                elseif (is_dir($link)) rmdir($link);
                else unlink($link);
            }
        }
    }

    protected function links($links = null)
    {
        if (!is_null($links)) {
            $this->laravel['config']['filesystems.links'] = $links;
        }
        return $this->laravel['config']['filesystems.links'] ??
            [public_path('storage') => storage_path('app/public')];
    }
}