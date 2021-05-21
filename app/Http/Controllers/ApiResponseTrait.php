<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Exceptions\UnhandledException;
use App\Exceptions\UserException;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Support\Facades\App;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait ApiResponseTrait
{
    protected static $extraResponse = null;

    public static function addBlockResponseMessage($message, $fresh = false)
    {
        if (is_null(static::$extraResponse)) {
            static::$extraResponse = [];
        }
        if ($fresh || !isset(static::$extraResponse['_block']) || static::$extraResponse['_block'] == null) {
            static::$extraResponse['_block'] = [];
        }
        static::$extraResponse['_block'][] = $message;
    }

    public static function addErrorResponseMessage($level, $data)
    {
        if (is_null(static::$extraResponse)) {
            static::$extraResponse = [];
        }
        static::$extraResponse['_error'] = [
            'level' => $level,
            'data' => got($data),
        ];
    }

    /**
     * @param array|null $data
     * @param Throwable|array|string|null $message
     * @return array
     */
    protected static function payload($data = null, $message = null)
    {
        $debug = null;
        if ($message instanceof Throwable) {
            $exception = $message;
            if ($exception instanceof OAuthServerException) {
                $exception = UserException::from(
                    $exception,
                    trans('passport.' . $exception->getErrorType() . ($exception->getCode() == 8 ? '_refresh_token' : ''))
                );
            }
            if (!($exception instanceof Exception)) {
                $exception = UnhandledException::from($exception);
            }
            $debug = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraces(),
            ];
            $message = $exception->getMessages();
            static::addErrorResponseMessage($exception->getLevel(), $exception->getAttachedData());
        }

        return [
            '_messages' => $message ? (array)$message : null,
            '_data' => $data,
            '_extra' => static::$extraResponse,
            '_exception' => App::runningInDebug() ? $debug : null,
        ];
    }

    public static function failPayload($data = null, $message = null, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $errorCode = 0)
    {
        return array_merge(static::payload($data, $message), [
            '_status' => false,
            '_code' => $statusCode,
            '_error' => $errorCode ?: $statusCode,
        ]);
    }

    public static function successPayload($data = null, $message = null, $statusCode = Response::HTTP_OK)
    {
        return array_merge(static::payload($data, $message), [
            '_status' => true,
            '_code' => $statusCode,
        ]);
    }

    protected function withInlineMiddleware()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $this->inlineMiddleware($request);
            return $next($request);
        });
    }

    protected function inlineMiddleware(Request $request = null)
    {
    }

    /**
     * @param array $payload
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function response($payload, $status = Response::HTTP_OK, $headers = [])
    {
        return response()->json(
            $payload,
            ConfigHelper::getApiResponseStatus($status),
            ConfigHelper::getApiResponseHeaders($headers),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * @param array|null $data
     * @param array|string|null $message
     * @param array $headers
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function responseSuccess($data = null, $message = null, $headers = [], $statusCode = Response::HTTP_OK)
    {
        $this->transactionComplete();
        return $this->response(
            static::successPayload(
                $data,
                $message,
                $statusCode
            ),
            $statusCode,
            $headers
        );
    }

    /**
     * @param Exception|array|string|null $message
     * @param array|null $data
     * @param int $statusCode
     * @param int $errorCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function responseFail($message = null, $data = null, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $errorCode = 0, $headers = [])
    {
        $this->transactionStop();
        if ($message instanceof \Exception) {
            Log::error($message);
        }
        if ($message instanceof HttpExceptionInterface) {
            $statusCode = $message->getStatusCode();
            $errorCode = $message->getCode();
        }
        return $this->response(
            static::failPayload(
                $data,
                $message,
                $statusCode,
                $errorCode
            ),
            $statusCode,
            $headers
        );
    }

    protected function getRespondedDataWithKey($data, $key = null)
    {
        return is_null($key) ? $data : [
            $key => $data,
        ];
    }
}
