<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\Exceptions\ConsoleException;
use App\Exceptions\Exception;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\ConsoleClientTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use App\Utils\ShellTrait;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Command extends BaseCommand
{
    use ClassTrait, ShellTrait, ConsoleClientTrait, TransactionTrait;

    protected $__friendlyName;

    protected function __friendlyName()
    {
        if (empty($this->__friendlyName)) {
            $this->__friendlyName = trim(preg_replace('/command$/i', '', static::__friendlyClassBaseName()));
        }
        return $this->__friendlyName;
    }

    protected $noInformation = false;

    public function __construct()
    {
        parent::__construct();

        $this->consoleClientApply();
    }

    protected function setStyles()
    {
        if (!$this->output->getFormatter()->hasStyle('caution')) {
            $style = new OutputFormatterStyle('red');

            $this->output->getFormatter()->setStyle('caution', $style);
        }
        if (!$this->output->getFormatter()->hasStyle('strong-caution')) {
            $style = new OutputFormatterStyle('black', 'red');

            $this->output->getFormatter()->setStyle('strong-caution', $style);
        }
    }

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

    public function alert($string)
    {
        $this->newLine();
        parent::alert($string);
    }

    public function before()
    {
        if (!$this->noInformation) {
            $this->newLine();
            $this->info(sprintf('START %s...', strtoupper($this->__friendlyClassBaseName())));
            $this->newLine();
        }
        return $this;
    }

    public function after()
    {
        if (!$this->noInformation) {
            $this->newLine();
            $this->info(sprintf('END %s!!!', strtoupper($this->__friendlyClassBaseName())));
        }
        return $this;
    }

    public function start()
    {
        Log::info(sprintf('%s commanding...', static::class));
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s commanded!', static::class));
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this;
    }

    public function handle()
    {
        $this->setStyles();
        $this->start()->before();
        try {
            $this->go();
        } catch (Throwable $e) {
            $this->handleException($e);
        }
        $this->after()->end();
    }

    protected function handleException(Throwable $e)
    {
        throw ($e instanceof ConsoleException ?
            $e : ConsoleException::from($e)->setCommand($this));
    }

    public function caution($string, $verbosity = null)
    {
        $this->line($string, 'caution', $verbosity);
    }

    public function renderThrowable(Throwable $e)
    {
        $this->output->writeln(sprintf('<strong-caution>EXCEPTION: %s</strong-caution>', get_class($e)), $this->parseVerbosity());
        $this->output->writeln('<comment>Code:</comment> ' . $e->getCode(), $this->parseVerbosity());
        $this->output->writeln('<comment>Message:</comment> ' . $e->getMessage(), $this->parseVerbosity());
        $this->output->writeln(sprintf('<comment>File:</comment> [%s:%d]', $e->getFile(), $e->getLine()), $this->parseVerbosity());
        if ($e instanceof Exception) {
            $this->warn('Data:');
            var_dump($e->getAttachedData());
        }
        $this->warn('Trace:');
        $previous = false;
        do {
            if ($previous) {
                $this->output->writeln(
                    '<caution>[previous exception]</caution>',
                    $this->parseVerbosity()
                );
            } else {
                $this->output->writeln(
                    '<caution>[stacktrace]</caution>',
                    $this->parseVerbosity()
                );
            }
            $last = 0;
            foreach ($e->getTrace() as $i => $trace) {
                if (isset($trace['file'])) {
                    $this->output->writeln(
                        sprintf(
                            '<comment>#%d</comment> <info>%s:%s</info> :',
                            $i,
                            isset($trace['file']) ? $trace['file'] : '',
                            isset($trace['line']) ? $trace['line'] : ''
                        ),
                        $this->parseVerbosity()
                    );
                    $this->output->writeln(
                        sprintf(
                            '%s %s%s%s()',
                            str_repeat(' ', strlen($i) + 1),
                            isset($trace['class']) ? $trace['class'] : '',
                            isset($trace['type']) ? $trace['type'] : '',
                            isset($trace['function']) ? $trace['function'] : ''
                        ),
                        $this->parseVerbosity()
                    );
                }
                else {
                    $this->output->writeln(
                        sprintf(
                            '<comment>#%d</comment> %s%s%s()',
                            $i,
                            isset($trace['class']) ? $trace['class'] : '',
                            isset($trace['type']) ? $trace['type'] : '',
                            isset($trace['function']) ? $trace['function'] : ''
                        ),
                        $this->parseVerbosity()
                    );
                }
                $last = $i + 1;
            }
            $this->output->writeln(sprintf('<comment>#%d</comment> {main}', $last), $this->parseVerbosity());
        } while (($e = $e->getPrevious()) && ($previous = true));
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

        Log::info(sprintf(
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
