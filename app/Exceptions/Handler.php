<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use App\Utils\ConfigHelper;
use App\Utils\Database\Transaction\TransactionManager;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
//        'current_password',
//        'password',
//        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $e)
    {
        TransactionManager::getInstance()->stop();

        parent::report($e);
    }

    protected function prepareException(Throwable $e)
    {
        // common
        $exceptionClasses = [
            NotFoundHttpException::class,
            ThrottleRequestsException::class,
        ];
        foreach ($exceptionClasses as $exceptionClass) {
            if ($e instanceof $exceptionClass) {
                return AppException::from($e);
            }
        }
        // specific
        if ($e instanceof MethodNotAllowedHttpException) {
            return AppException::from($e, [], [
                'trans_options' => [
                    'replace' => [
                        'method' => request()->getMethod(),
                        'allow' => $e->getHeaders()['Allow'],
                    ],
                ],
            ]);
        }
        return parent::prepareException($e);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            ConfigHelper::getApiResponseStatus($this->isHttpException($e) ? $e->getStatusCode() : 500),
            array_merge($this->isHttpException($e) ? $e->getHeaders() : [], ConfigHelper::getApiResponseHeaders()),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        return ApiController::failPayload(null, $e, $this->isHttpException($e) ? $e->getStatusCode() : 500, $e->getCode());
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(
                ApiController::failPayload(null, $exception, 401),
                ConfigHelper::getApiResponseStatus(401),
                ConfigHelper::getApiResponseHeaders(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json(
            ApiController::failPayload(null, $exception, $exception->status),
            ConfigHelper::getApiResponseStatus($exception->status),
            ConfigHelper::getApiResponseHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    #region Console
    public function renderForConsole($output, Throwable $e)
    {
        $command = $e instanceof ConsoleException ? $e->getCommand() : null;

        if (method_exists($command, 'renderThrowable')) {
            $command->renderThrowable($e);
        } else {
            parent::renderForConsole($output, $e);
        }

        if ($command) {
            $command->fails();
        }
    }
    #endregion
}
