<?php

namespace App\Console\Commands\Base;

use App\Utils\ClassTrait;
use App\Utils\LogHelper;
use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    use ClassTrait;

    protected $noInformation = false;

    protected function lineBreak()
    {
        $this->line('');
    }

    public function alert($string)
    {
        $this->lineBreak();
        parent::alert($string);
    }

    protected function before()
    {
        if (!$this->noInformation) {
            $this->lineBreak();
            $this->info(sprintf('START %s...', strtoupper($this->__friendlyClassBaseName())));
            $this->lineBreak();
        }
    }

    public function handle()
    {
        LogHelper::info(sprintf('%s executing...', static::class));
        try {
            $this->go();
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
        LogHelper::info(sprintf('%s executed!', static::class));
    }

    protected function handleException(\Exception $exception)
    {
        LogHelper::error($exception);

        $this->error('EXCEPTION:');
        $this->warn('- Message: ' . $exception->getMessage());
        $this->warn('- File: ' . $exception->getFile());
        $this->warn('- Line:' . $exception->getLine());
        $this->warn('- Trace:');
        $this->warn($exception->getTraceAsString());
    }

    protected abstract function go();

    protected function after()
    {
        if (!$this->noInformation) {
            $this->lineBreak();
            $this->info(sprintf('END %s!!!', strtoupper($this->__friendlyClassBaseName())));
        }
    }
}
