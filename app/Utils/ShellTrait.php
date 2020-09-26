<?php

namespace App\Utils;

use Symfony\Component\Process\Process;

trait ShellTrait
{
    /**
     * @var Process
     */
    protected $shellProcess;

    /**
     * @var int
     */
    protected $shellExitCode;

    protected function shellSuccess()
    {
        return $this->shellExitCode == 0;
    }

    protected function shell($cmd, callable $outputSuccessCallback = null, callable $outputErrorCallback = null)
    {
        $this->shellProcess = Process::fromShellCommandline($cmd, base_path(), null, null, null);
        $this->shellExitCode = $this->shellProcess->run(function ($type, $buffer) use ($outputSuccessCallback, $outputErrorCallback) {
            if (Process::ERR === $type) {
                $outputErrorCallback && $outputErrorCallback($buffer);
            } else {
                $outputSuccessCallback && $outputSuccessCallback($buffer);
            }
        });
        return $this->shellExitCode;
    }

    protected function shellOutput()
    {
        return $this->shellSuccess() ? $this->shellProcess->getOutput() : $this->shellProcess->getErrorOutput();
    }
}