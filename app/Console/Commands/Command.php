<?php

namespace App\Console\Commands;

use App\Utils\ClassTrait;
use App\Utils\LogHelper;
use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    use ClassTrait;

    protected $noInformation = false;

    protected $friendlyName;

    protected function lineBreak()
    {
        $this->line('');
    }

    public function getFriendlyName()
    {
        if (empty($this->friendlyName)) {
            $this->friendlyName = $this->__friendlyClassBaseName();
        }
        return $this->friendlyName;
    }

    protected function before()
    {
        $this->lineBreak();
        $this->info(sprintf('START %s...', strtoupper($this->getFriendlyName())));
        $this->lineBreak();
    }

    public function handle()
    {
        LogHelper::info(sprintf('%s executing...', static::class));

        if ($this->noInformation) {
            $this->catchGo();
        } else {
            $this->before();
            $this->catchGo();
            $this->after();
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

    protected function catchGo()
    {
        try {
            $this->go();
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    protected abstract function go();

    protected function after()
    {
        $this->lineBreak();
        $this->info(sprintf('END %s!!!', strtoupper($this->getFriendlyName())));
    }
}
