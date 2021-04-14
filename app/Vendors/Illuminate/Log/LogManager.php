<?php

namespace App\Vendors\Illuminate\Log;

use App\Exceptions\Exception;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Log\LogManager as BaseLogManager;
use Illuminate\Support\Facades\Auth;

class LogManager extends BaseLogManager
{
    protected function formatMessage($message)
    {
        if ($message instanceof \Throwable) {
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

        if (!isset($context['userId'])) {
            $context['userId'] = Auth::id();
        }

        $exceptionContent = [];
        if (!isset($context['exception']) && $message instanceof \Throwable) {
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
        parent::emergency($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function alert($message, array $context = [])
    {
        parent::alert($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function critical($message, array $context = [])
    {
        parent::critical($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function error($message, array $context = [])
    {
        parent::error($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function warning($message, array $context = [])
    {
        parent::warning($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function notice($message, array $context = [])
    {
        parent::notice($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function info($message, array $context = [])
    {
        parent::info($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function debug($message, array $context = [])
    {
        parent::debug($this->formatMessage($message), $this->formatContext($context, $message));
    }

    public function log($level, $message, array $context = [])
    {
        parent::log($level, $this->formatMessage($message), $this->formatContext($context, $message));
    }
}