<?php

namespace App\Vendors\Monolog\Formatter;

use Monolog\Formatter\LineFormatter as BaseLineFormatter;
use Monolog\Utils;

class LineFormatter extends BaseLineFormatter
{
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n%traces%";

    protected $varTraces = [];

    public function format(array $record): string
    {
        $output = parent::format($record);
        if (count($this->varTraces) > 0) {
            $output = str_replace('%traces%', implode("\n", $this->varTraces), $output) . "\n";
            $this->varTraces = [];
            return $output;
        }
        return str_replace('%traces%', '', $output);
    }

    protected function normalizeException(\Throwable $e, int $depth = 0): string
    {
        $this->traceException($e);
        return $e->getMessage();
    }

    protected function traceException(\Throwable $e, $previous = false)
    {
        $this->varTraces[] = sprintf('%s: %s', $previous ? 'PREVIOUS EXCEPTION' : 'EXCEPTION', Utils::getClass($e));
        $this->varTraces[] = sprintf('Code: %s', $e->getCode());
        if ($e instanceof \SoapFault) {
            if (isset($e->faultcode)) {
                $this->varTraces[] = sprintf('Fault code: %s', $e->faultcode);
            }
            if (isset($e->faultactor)) {
                $this->varTraces[] = sprintf('Fault actor: %s', $e->faultactor);
            }
            if (isset($e->detail)) {
                if (is_string($e->detail)) {
                    $this->varTraces[] = sprintf('Fault detail: %s', $e->detail);
                }
                elseif (is_object($e->detail) || is_array($e->detail)) {
                    $this->varTraces[] = sprintf('Fault detail: %s', $this->toJson($e->detail, true));
                }
            }
        }
        $this->varTraces[] = sprintf('Message: %s', $e->getMessage());
        $this->varTraces[] = sprintf('File: [%s:%s]', $e->getFile(), $e->getLine());
        $this->varTraces[] = sprintf('Trace:');
        $last = 0;
        foreach ($e->getTrace() as $i => $trace) {
            if (isset($trace['file'])) {
                $this->varTraces[] = sprintf(
                    '#%d [%s:%s]',
                    $i,
                    $trace['file'] ?? '',
                    $trace['line'] ?? ''
                );
                $this->varTraces[] = sprintf(
                    '%s %s%s%s()',
                    str_repeat(' ', strlen($i) + 1),
                    $trace['class'] ?? '',
                    $trace['type'] ?? '',
                    $trace['function'] ?? ''
                );
            }
            else {
                $this->varTraces[] = sprintf(
                    '#%d %s%s%s()',
                    $i,
                    $trace['class'] ?? '',
                    $trace['type'] ?? '',
                    $trace['function'] ?? ''
                );
            }
            $last = $i + 1;
        }
        $this->varTraces[] = sprintf('#%d {main}', $last);
        if ($e = $e->getPrevious()) {
            $this->varTraces[] = str_repeat('-', 10);
            $this->traceException($e, true);
        }
    }
}
