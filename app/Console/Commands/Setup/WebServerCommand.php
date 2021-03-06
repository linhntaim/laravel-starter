<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Setup;

class WebServerCommand extends Command
{
    protected $signature = 'setup:web-server {--u} {--f}';

    protected function goInstalling()
    {
        $forced = $this->forced();
        if ($forced || !file_exists(public_path('.htaccess'))) {
            copy(public_path('.htaccess.example'), public_path('.htaccess'));
        }
        if ($forced || !file_exists(public_path('.htpasswd'))) {
            copy(public_path('.htpasswd.example'), public_path('.htpasswd'));
        }
        if ($forced || !file_exists(public_path('web.config'))) {
            copy(public_path('web.config.example'), public_path('web.config'));
        }
        if ($forced || !file_exists(public_path('robots.txt'))) {
            copy(public_path('robots.txt.example'), public_path('robots.txt'));
        }
    }

    protected function goUninstalling()
    {
        if (file_exists(public_path('.htaccess'))) {
            unlink(public_path('.htaccess'));
        }
        if (file_exists(public_path('.htpasswd'))) {
            unlink(public_path('.htpasswd'));
        }
        if (file_exists(public_path('web.config'))) {
            unlink(public_path('web.config'));
        }
        if (file_exists(public_path('robots.txt'))) {
            unlink(public_path('robots.txt'));
        }
    }
}