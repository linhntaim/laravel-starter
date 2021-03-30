<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\ModelRepositories\HandledFileRepository;
use App\Models\HandledFile;
use App\Utils\TransactionTrait;

class ScanHandledFilesCommand extends Command
{
    use TransactionTrait;

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
        } catch (\Exception $exception) {
            $this->transactionStop();

            throw $exception;
        }
    }
}