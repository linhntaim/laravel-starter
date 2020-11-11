<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\Utils\ClassTrait;
use App\Utils\LogHelper;
use App\Utils\ShellTrait;
use Illuminate\Console\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    use ClassTrait, ShellTrait;

    protected $__friendlyName;

    protected function __friendlyName()
    {
        if (empty($this->__friendlyName)) {
            $this->__friendlyName = trim(preg_replace('/command$/i', '', static::__friendlyClassBaseName()));
        }
        return $this->__friendlyName;
    }

    protected $noInformation = false;

    /**
     * @return Command
     */
    public function disableInformation()
    {
        $this->noInformation = true;
        return $this;
    }

    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        $arguments['command'] = $command;

        $commander = $this->resolveCommand($command);

        if ($commander instanceof Command) {
            $commander->disableInformation();
        }

        return $commander->run(
            $this->createInputFromArguments($arguments), $output
        );
    }

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

    protected function after()
    {
        if (!$this->noInformation) {
            $this->lineBreak();
            $this->info(sprintf('END %s!!!', strtoupper($this->__friendlyClassBaseName())));
        }
    }

    public function handle()
    {
        LogHelper::info(sprintf('%s executing...', static::class));
        try {
            $this->before();
            $this->go();
            $this->after();
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

    protected function goShell($shell)
    {
        $this->warn('SHELL EXECUTING...');
        $this->info($shell);
        $this->line('--------');

        $exitCode = $this->shell(
            $shell,
            function ($buffer) {
                echo $buffer;
            },
            function ($buffer) {
                $this->warn(trim($buffer));
            }
        );

        $this->line('--------');
        $this->shellSuccess() ?
            $this->info('Exit code: ' . $exitCode) : $this->error('Exit code: ' . $exitCode);
        $this->info(sprintf('SHELL %s!!!', $this->shellSuccess() ? 'EXECUTED' : 'FAILED'));

        LogHelper::info(sprintf(
            'Shell %s:' . PHP_EOL
            . '%s' . PHP_EOL
            . '--------' . PHP_EOL
            . '%s' . PHP_EOL
            . '--------' . PHP_EOL
            . 'Exit code: %d.',
            $this->shellSuccess() ? 'executed' : 'failed',
            $shell,
            trim($this->shellOutput()),
            $exitCode
        ));
    }
}
