<?php

namespace App\Exceptions;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\TransactionHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        TransactionHelper::getInstance()->stop();

        return parent::render($request, $exception);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            ConfigHelper::getApiResponseStatus($this->isHttpException($e) ? $e->getStatusCode() : 500),
            ConfigHelper::getApiResponseHeaders($this->isHttpException($e) ? $e->getHeaders() : []),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        return ApiController::failPayload(null, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(
                ApiController::failPayload(null, $exception),
                ConfigHelper::getApiResponseStatus(401),
                ConfigHelper::getApiResponseHeaders(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json(
            ApiController::failPayload(null, $exception),
            ConfigHelper::getApiResponseStatus($exception->status),
            ConfigHelper::getApiResponseHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
