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
        $this->protectTransaction($e);

        parent::report($e);
    }

    protected function protectTransaction(Throwable $e)
    {
        TransactionManager::getInstance()->stop();
        return $this;
    }

    protected function prepareException(Throwable $e)
    {
        // TODO: Specific

        // TODO
        $exceptionClasses = [
            NotFoundHttpException::class,
            ThrottleRequestsException::class,
            MethodNotAllowedHttpException::class,
        ];
        foreach ($exceptionClasses as $exceptionClass) {
            if ($e instanceof $exceptionClass) {
                return AppException::from($e);
            }
        }
        return parent::prepareException($e);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return responseJson(
            $this->convertExceptionToArray($e),
            ConfigHelper::getApiResponseStatus($this->isHttpException($e) ? $e->getStatusCode() : 500),
            array_merge($this->isHttpException($e) ? $e->getHeaders() : [], ConfigHelper::getApiResponseHeaders())
        );
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        return ApiController::failPayload(null, $e, $this->isHttpException($e) ? $e->getStatusCode() : 500, $e->getCode());
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? responseJson(
                ApiController::failPayload(null, $exception, 401),
                ConfigHelper::getApiResponseStatus(401),
                ConfigHelper::getApiResponseHeaders()
            )
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return responseJson(
            ApiController::failPayload(null, $exception, $exception->status),
            ConfigHelper::getApiResponseStatus($exception->status),
            ConfigHelper::getApiResponseHeaders()
        );
    }

    #region Console
    public function renderForConsole($output, Throwable $e)
    {
        $command = $e instanceof ConsoleException ? $e->getCommand() : null;

        if (method_exists($command, 'renderThrowable')) {
            $command->renderThrowable($e);
        }
        else {
            parent::renderForConsole($output, $e);
        }

        if ($command) {
            $command->fails();
        }
    }
    #endregion
}
