<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Make;

class TryCommand extends Command
{
    protected $signature = 'make:command:try {--f}';

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
        return app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'TryCommand.php']));
    }

    protected function exampleTryFilePath()
    {
        return app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'TryCommand.php.example']));
    }
}
