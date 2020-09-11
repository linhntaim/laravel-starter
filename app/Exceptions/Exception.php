<?php

namespace App\Exceptions;

use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use Exception as BaseException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

abstract class Exception extends BaseException implements HttpExceptionInterface
{
    use ClassTrait;

    const LEVEL = 4;
    const CODE = 500;

    /**
     * @param Throwable $exception
     * @return Exception
     */
    public static function from($exception)
    {
        $class = static::__class();
        return new $class(null, 0, $exception);
    }

    protected static function getThrowableMessage(Throwable $throwable)
    {
        if ($throwable instanceof PDOException) {
            return $throwable->errorInfo[2];
        }
        return $throwable->getMessage();
    }

    protected $attachedData;
    protected $messages;

    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        $withoutMessage = empty($message);

        if (is_array($message)) {
            $this->messages = $message;
            $message = array_values($this->messages)[0];
        } elseif (!empty($message)) {
            $message = $this->formatMessage($message);
            $this->messages = [$message];
        }

        parent::__construct($message, $code, $previous);

        if ($previous) {
            $this->line = $previous->getLine();
            $this->file = $previous->getFile();
            if (empty($message)) {
                $this->message = $this->formatMessage(static::getThrowableMessage($previous));
                $this->messages = [$this->message];
            }
        }

        if (!config('app.debug') && ConfigHelper::get('force_common_exception') && $withoutMessage) {
            $this->message = trans('error.exceptions.default_exception.level_failed');
            $this->messages = [$this->message];
        }
    }

    public function getStatusCode()
    {
        return static::CODE;
    }

    public function getHeaders()
    {
        return [];
    }

    public function setEmptyMessage()
    {
        $this->message = null;
        $this->messages = [];
        return $this;
    }

    public function setAttachedData($attachedData)
    {
        if (empty($this->attachedData)) {
            $this->attachedData = [];
        }
        $this->attachedData = array_merge($this->attachedData, $attachedData);
        return $this;
    }

    /**
     * @param $title
     * @param null $description
     * @return Exception
     */
    public function setTitleAndDescription($title, $description = null)
    {
        return $this->setAttachedData($description ? [
            '_title' => $title,
            '_description' => $description,
        ] : [
            '_title' => $title,
        ]);
    }

    public function getAttachedData()
    {
        return $this->attachedData;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getLevel()
    {
        return static::LEVEL;
    }

    public function formatMessage($message = '')
    {
        return empty($message) ?
            $this->__transErrorWithModule('level_failed')
            : $this->__transErrorWithModule('level', ['message' => $message]);
    }
}
