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
use Throwable;

abstract class Exception extends BaseException implements HttpExceptionInterface
{
    use ClassTrait;

    public const LEVEL = 4;
    public const CODE = 500;

    /**
     * @param Throwable $exception
     * @param string $message
     * @param array $publicAttachedData
     * @param array $privateAttachedData
     * @return Exception
     */
    public static function from($exception, $message = '', $publicAttachedData = [], $privateAttachedData = [])
    {
        $class = static::__class();
        return new $class($message, 0, $exception, $publicAttachedData, $privateAttachedData);
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

    public function __construct($message = '', $code = 0, Throwable $previous = null, $publicAttachedData = [], $privateAttachedData = [])
    {
        parent::__construct('', $code ? $code : static::CODE, $previous);

        if ($previous) {
            $this->code = $previous instanceof HttpExceptionInterface ?
                $previous->getStatusCode() : $previous->getCode();
            $this->file = $previous->getFile();
            $this->line = $previous->getLine();
        }

        if (empty($message)) {
            if (!App::runningInDebug() && ConfigHelper::get('force_common_exception')) {
                $this->messages = [trans('error.exceptions.default_exception.level_failed')];
            } elseif ($message = $this->getMessageFromPrevious()) {
                $this->messages = [$message];
            } else {
                $this->messages = [$this->defaultMessage()];
            }
        } else {
            if (is_array($message)) {
                $this->messages = $message;
            } else {
                $this->messages = [$message];
            }
        }
        $this->message = $this->formatMessage(array_values($this->messages)[0]);

        $this->attachedData = [
            'public' => $publicAttachedData,
            'private' => $privateAttachedData,
        ];
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
        } else {
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

    public function getAttachedData()
    {
        return $this->attachedData['public'];
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
        $transOptions = isset($this->attachedData['private']['trans_options']) ?
            $this->attachedData['private']['trans_options'] : [];
        return transIf(
            'error.def.abort.' . $this->code,
            $this->__transErrorWithModule('level_failed'),
            isset($transOptions['replace']) ? $transOptions['replace'] : [],
            isset($transOptions['locale']) ? $transOptions['locale'] : null
        );
    }
}
