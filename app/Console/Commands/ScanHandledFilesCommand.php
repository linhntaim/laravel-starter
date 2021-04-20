<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;
use Throwable;

class ScanHandledFilesCommand extends Command
{
    protected $signature = 'scan:handled_files';

    protected function go()
    {
        foreach ((new HandledFileRepository())->getForScanning() as $handledFile) {
            $this->scan($handledFile);
        }
    }

    protected function scan(HandledFile $handledFile)
    {
        $this->transactionStart();
        try {
            (new HandledFileRepository())->withModel($handledFile)->scan();
            $this->transactionComplete();
        } catch (Throwable $exception) {
            $this->transactionStop();

            throw $exception;
        }
    }
}