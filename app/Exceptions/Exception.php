<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
            return is_array($throwable->errorInfo) && isset($throwable->errorInfo[2])
                ? $throwable->errorInfo[2] : $throwable->getMessage();
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
        } elseif (!$withoutMessage) {
            $message = $this->formatMessage($message);
        }

        parent::__construct($message, $code ? $code : static::CODE, $previous);

        if ($withoutMessage) {
            if (!config('app.debug') && ConfigHelper::get('force_common_exception')) {
                $this->message = trans('error.exceptions.default_exception.level_failed');
            } elseif ($previous) {
                $this->message = $this->formatMessage(static::getThrowableMessage($previous));
            } else {
                $this->message = $this->formatMessage();
            }
        }
        $this->messages = [$this->message];

        if ($previous) {
            $this->line = $previous->getLine();
            $this->file = $previous->getFile();
        }
    }

    public function getStatusCode()
    {
        return $this->getCode();
    }

    public function getHeaders()
    {
        return [];
    }

    public function transformMessage(callable $transformCallback)
    {
        $this->message = $transformCallback($this->message);
        foreach ($this->messages as &$message) {
            $message = $transformCallback($message);
        }
        return $this;
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
