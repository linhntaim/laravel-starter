<?php

namespace App\Vendors\Illuminate\Log;

use App\Exceptions\Exception;
use App\Vendors\Illuminate\Support\Facades\App;
use App\Vendors\Monolog\Formatter\LineFormatter;
use Illuminate\Log\LogManager as BaseLogManager;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LogManager extends BaseLogManager
{
    protected $off = false;

    protected function off()
    {
        $this->off = true;
        return $this;
    }

    protected function on()
    {
        $this->off = false;
        return $this;
    }

    protected function formatMessage($message)
    {
        if ($message instanceof Throwable) {
            $message = $message->getMessage();
        }
        return sprintf('%s %s', $this->app['id'], $message);
    }

    protected function formatContext($context, $message)
    {
        $request = request();
        $requestContext = [
            'ips' => $request->ips(),
            'userAgent' => $request->userAgent(),
        ];

        if (App::runningFromRequest()) {
            if (!isset($context['userId'])) {
                $this->off();
                $context['userId'] = Auth::id();
                $this->on();
            }
        }

        $exceptionContent = [];
        if (!isset($context['exception']) && $message instanceof Throwable) {
            if (method_exists($message, 'context')) {
                $exceptionContent = $message->context();
            }
            $requestContext['inputs'] = $request->all();
            $context['exception'] = $message;
            if ($message instanceof Exception) {
                $context['exceptionData'] = $message->getAttachedData();
            }
        }
        if (App::runningFromRequest()) {
            $context['request'] = $requestContext;
        }
        return array_merge($context, $exceptionContent, $context);
    }

    public function emergency($message, array $context = [])
    {
        if ($this->off) return;
        parent::emergency($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function alert($message, array $context = [])
    {
        if ($this->off) return;
        parent::alert($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function critical($message, array $context = [])
    {
        if ($this->off) return;
        parent::critical($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function error($message, array $context = [])
    {
        if ($this->off) return;
        parent::error($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function warning($message, array $context = [])
    {
        if ($this->off) return;
        parent::warning($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function notice($message, array $context = [])
    {
        if ($this->off) return;
        parent::notice($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function info($message, array $context = [])
    {
        if ($this->off) return;
        parent::info($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function debug($message, array $context = [])
    {
        if ($this->off) return;
        parent::debug($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function log($level, $message, array $context = [])
    {
        if ($this->off) return;
        parent::log($level, $this->formatMessage($message), $this->formatContext($context, $message));
    }

    protected function formatter()
    {
        return tap(new LineFormatter(null, $this->dateFormat, true, true), function ($formatter) {
            $formatter->includeStacktraces();
        });
    }
}
