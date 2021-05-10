<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Make\Controller;

use App\Console\Commands\Make\Command;

class TestApiCommand extends Command
{
    protected $signature = 'make:controller:test-api {--f}';

    protected $tryFilePath;

    protected function goMaking()
    {
        $this->tryFilePath = $this->tryFilePath();
        if ($this->has()) {
            if ($this->forced()) {
                $this->remove();
                $this->copy();
            }
        }
        else {
            $this->copy();
        }
    }

    protected function remove()
    {
        unlink($this->tryFilePath);
    }

    protected function has()
    {
        return file_exists($this->tryFilePath);
    }

    protected function copy()
    {
        copy($this->exampleTryFilePath(), $this->tryFilePath);
    }

    protected function tryFilePath()
    {
        return app_path(implode(DIRECTORY_SEPARATOR, ['Http', 'Controllers', 'Api', 'TestApiController.php']));
    }

    protected function exampleTryFilePath()
    {
        return app_path(implode(DIRECTORY_SEPARATOR, ['Http', 'Controllers', 'Api', 'TestApiController.php.example']));
    }
}
