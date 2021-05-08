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
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Command extends BaseCommand
{
    use ClassTrait, ShellTrait, ConsoleClientTrait, TransactionTrait;

    protected static $shoutOutEnabled = true;

    public static function currentShoutOut()
    {
        return self::$shoutOutEnabled;
    }

    public static function setShoutOut($shoutOut = true)
    {
        self::$shoutOutEnabled = $shoutOut;
    }

    public static function disableShoutOut()
    {
        self::$shoutOutEnabled = false;
    }

    public static function enableShoutOut()
    {
        self::$shoutOutEnabled = true;
    }

    protected $friendlyName;

    public function __construct()
    {
        parent::__construct();

        $this->friendlyName = trim(preg_replace('/command$/i', '', static::__friendlyClassBaseName()));

        $this->consoleClientApply();
    }

    public function ifOption($key, &$option, $filled = false)
    {
        $option = $this->option($key);
        return !is_null($option) && (!$filled || filled($option));
    }

    public function optionOr($key, $default = null, $filled = true)
    {
        return got($this->option($key), $default, $filled);
    }

    public function ifArgument($key, &$argument, $filled = false)
    {
        $argument = $this->argument($key);
        return !is_null($argument) && (!$filled || filled($argument));
    }

    public function argumentOr($key, $default = null, $filled = true)
    {
        return got($this->argument($key), $default, $filled);
    }

    public function alert($string)
    {
        $this->newLine();
        parent::alert($string);
    }

    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        $origin = self::currentShoutOut();
        self::disableShoutOut();
        $run = parent::runCommand($command, $arguments, $output);
        self::setShoutOut($origin);
        return $run;
    }

    protected function friendlyName()
    {
        return $this->friendlyName;
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
        return $this;
    }

    public function start()
    {
        Log::info(sprintf('%s commanding...', static::class));
        return $this->setStyles()
            ->shoutOutAtStart();
    }

    protected function shoutOutAtStart()
    {
        if (App::runningInConsole() && self::$shoutOutEnabled) {
            $this->newLine();
            $this->info(sprintf('START %s...', strtoupper($this->__friendlyClassBaseName())));
            $this->newLine();
        }
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s commanded!', static::class));
        return $this->shoutOutAtEnd();
    }

    protected function shoutOutAtEnd()
    {
        if (App::runningInConsole() && self::$shoutOutEnabled) {
            $this->newLine();
            $this->info(sprintf('END %s!!!', strtoupper($this->__friendlyClassBaseName())));
        }
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this->shoutOutAtEnd();
    }

    public function handle()
    {
        try {
            $this->start();
            $this->go();
            $this->end();
        }
        catch (Throwable $e) {
            $this->handleException($e);
        }
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

    public function renderThrowable(Throwable $e, $previous = false)
    {
        $this->output->writeln(sprintf('<strong-caution>%s: %s</strong-caution>', $previous ? 'PREVIOUS EXCEPTION' : 'EXCEPTION', get_class($e)), $this->parseVerbosity());
        $this->output->writeln(sprintf('<comment>Code:</comment> %s', $e->getCode()), $this->parseVerbosity());
        if ($e instanceof \SoapFault) {
            if (isset($e->faultcode)) {
                $this->output->writeln(sprintf('<comment>Fault code:</comment> %s', $e->faultcode), $this->parseVerbosity());
            }
            if (isset($e->faultactor)) {
                $this->output->writeln(sprintf('<comment>Fault actor:</comment> %s', $e->faultactor), $this->parseVerbosity());
            }
            if (isset($e->detail)) {
                if (is_string($e->detail)) {
                    $this->output->writeln(sprintf('<comment>Fault detail:</comment> %s', $e->detail), $this->parseVerbosity());
                }
                elseif (is_object($e->detail) || is_array($e->detail)) {
                    $this->output->writeln(sprintf('<comment>Fault detail:</comment> %s', json_encode($e->detail)), $this->parseVerbosity());
                }
            }
        }
        $this->output->writeln(sprintf('<comment>Message:</comment> %s', $e->getMessage()), $this->parseVerbosity());
        $this->output->writeln(sprintf('<comment>File:</comment> [%s:%d]', $e->getFile(), $e->getLine()), $this->parseVerbosity());
        if ($e instanceof Exception && count($data = $e->getAttachedData()) > 0) {
            $this->warn('Data:');
            var_dump($data);
        }
        $this->warn('Trace:');
        $last = 0;
        foreach ($e->getTrace() as $i => $trace) {
            if (isset($trace['file'])) {
                $this->output->writeln(
                    sprintf(
                        '<comment>#%d</comment> [<info>%s:%s</info>]',
                        $i,
                        $trace['file'] ?? '',
                        $trace['line'] ?? ''
                    ),
                    $this->parseVerbosity()
                );
                $this->output->writeln(
                    sprintf(
                        '%s %s%s%s()',
                        str_repeat(' ', strlen($i) + 1),
                        $trace['class'] ?? '',
                        $trace['type'] ?? '',
                        $trace['function'] ?? ''
                    ),
                    $this->parseVerbosity()
                );
            }
            else {
                $this->output->writeln(
                    sprintf(
                        '<comment>#%d</comment> %s%s%s()',
                        $i,
                        $trace['class'] ?? '',
                        $trace['type'] ?? '',
                        $trace['function'] ?? ''
                    ),
                    $this->parseVerbosity()
                );
            }
            $last = $i + 1;
        }
        $this->output->writeln(sprintf('<comment>#%d</comment> {main}', $last), $this->parseVerbosity());
        if ($e = $e->getPrevious()) {
            $this->line(str_repeat('-', 10));
            $this->renderThrowable($e, true);
        }
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
