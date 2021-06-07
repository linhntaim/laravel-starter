<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Support\Facades\App;
use Exception as BaseException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

abstract class Exception extends BaseException implements HttpExceptionInterface
{
    use ClassTrait;

    public const LEVEL = 4;
    public const CODE = 500;

    /**
     * @param Throwable $exception
     * @param string|string[]|array|null $message
     * @param array $publicAttachedData
     * @param array $privateAttachedData
     * @return Exception
     */
    public static function from(Throwable $exception, $message = '', $publicAttachedData = [], $privateAttachedData = [])
    {
        $class = static::__class();
        return new $class($message, 0, $exception, $publicAttachedData, $privateAttachedData);
    }

    /**
     * @param Throwable $exception
     * @return Throwable
     */
    public static function firstThrowable(Throwable $exception)
    {
        $first = $exception;
        while ($previous = $first->getPrevious()) {
            $first = $previous;
        }
        return $first;
    }

    public static function toArrayFrom(Throwable $e = null)
    {
        return $e ? array_merge(
            [
                'class' => get_class($e),
                'code' => $e->getCode(),
            ],
            $e instanceof Exception ? [
                'messages' => $e->getMessages(),
                'data' => $e->getAttachedData(null),
            ] : [
                'message' => $e->getMessage(),
            ],
            [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => static::traceToArrayFrom($e),
                'previous' => static::toArrayFrom($e->getPrevious()),
            ]
        ) : null;
    }

    public static function traceToArrayFrom(Throwable $e)
    {
        return array_map(function ($trace) {
            return array_merge(
                isset($trace['file']) ? [
                    'file' => $trace['file'],
                    'line' => $trace['line'],
                ] : [],
                isset($trace['class']) ? [
                    'class' => $trace['class'],
                    'type' => $trace['type'],
                ] : [],
                isset($trace['function']) ? [
                    'function' => $trace['function'],
                ] : [],
            );
        }, $e->getTrace());
    }

    /**
     * @var array
     */
    protected $messages;

    /**
     * @var array
     */
    protected $attachedData;

    /**
     * @var bool
     */
    protected $withMessageLevel = true;

    /**
     * Exception constructor.
     * @param string|string[]|array|null $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $publicAttachedData
     * @param array $privateAttachedData
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null, $publicAttachedData = [], $privateAttachedData = [])
    {
        parent::__construct('', 0, $previous);

        $this->attachedData = [
            'public' => $publicAttachedData,
            'private' => $privateAttachedData,
        ];

        if ($previous) {
            $this->code = method_exists($previous, 'getStatusCode') ?
                $previous->getStatusCode()
                : (method_exists($previous, 'getHttpStatusCode') ? $previous->getHttpStatusCode()
                    : $previous->getCode());
        }
        else {
            $this->code = $code ?: static::CODE;
        }

        if (is_null($message)) {
            $this->messages = [$this->defaultExceptionMessage()];
        }
        elseif (blank($message)) {
            if (!App::runningInDebug() && ConfigHelper::get('force_common_exception')) {
                $this->messages = [trans('error.exceptions.default_exception.level_failed')];
            }
            elseif ($message = $this->getMessageFromPrevious()) {
                $this->messages = [$message];
            }
            else {
                $this->messages = [$this->defaultMessage()];
            }
        }
        else {
            if (is_array($message)) {
                $this->messages = $message;
            }
            else {
                $this->messages = [$message];
            }
        }
        $this->message = $this->formatMessage(array_values($this->messages)[0]);
    }

    protected function getMessageFromPrevious()
    {
        $previous = $this->getPrevious();
        if ($previous) {
            if ($previous instanceof PDOException) {
                if (is_array($previous->errorInfo) && isset($previous->errorInfo[2])) {
                    return trim($previous->errorInfo[2]);
                }
            }
            if ($previous instanceof MethodNotAllowedHttpException) {
                $this->setAttachedData([
                    'trans_options' => [
                        'replace' => [
                            'method' => request()->getMethod(),
                            'allow' => $previous->getHeaders()['Allow'],
                        ],
                    ],
                ], false);
                return $this->defaultMessage();
            }
            return $previous->getMessage();
        }
        return '';
    }

    public function getStatusCode()
    {
        $code = $this->getCode();
        return $code >= 100 && $code < 600 ? $code : 500;
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

    /**
     * @param array $attachedData
     * @param bool $public
     * @return Exception
     */
    public function setAttachedData($attachedData, $public = true)
    {
        if ($public) {
            $this->attachedData['public'] = array_merge($this->attachedData['public'], $attachedData);
        }
        else {
            $this->attachedData['private'] = array_merge($this->attachedData['private'], $attachedData);
        }
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

    public function getAttachedData($scope = 'public')
    {
        return is_null($scope) ? $this->attachedData : $this->attachedData[$scope];
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getLevel()
    {
        return static::LEVEL;
    }

    protected function formatMessage($message)
    {
        return $this->withMessageLevel ? $this->__transErrorWithModule('level', ['message' => $message]) : $message;
    }

    protected function defaultMessage()
    {
        $transOptions = $this->attachedData['private']['trans_options'] ?? [];
        return transIf(
            'error.def.abort.' . $this->code,
            $this->defaultExceptionMessage(),
            $transOptions['replace'] ?? [],
            $transOptions['locale'] ?? null
        );
    }

    protected function defaultExceptionMessage()
    {
        return $this->__transErrorWithModule('level_failed');
    }

    public function toArray()
    {
        return static::toArrayFrom($this);
    }
}
